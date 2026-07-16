<?php

namespace App\Services;

use App\Events\SeatAvailabilityUpdated;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\BookingPassenger;
use App\Models\Coupon;
use App\Models\Holiday;
use App\Models\TicketBooking;
use App\Models\TicketOption;
use App\Models\TourSchedule;
use App\Models\User;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TourBookingService
{
    /**
     * Create a new tour booking
     *
     * @throws Exception
     */
    public function createBooking(array $data, User $user, string $sessionId): Booking
    {
        if (! empty($data['customer_phone'])) {
            $user->phone = $data['customer_phone'];
            $user->save();
        }

        return DB::transaction(function () use ($data, $user, $sessionId) {
            $totalPersons = $data['adults'] + $data['children'];
            $schedule = TourSchedule::with('tour')->lockForUpdate()->find($data['schedule_id']);

            $departureDateTime = Carbon::parse($schedule->departure_date->format('Y-m-d').' '.($schedule->tour->departure_time ?? '00:00:00'));
            if (! $schedule || $departureDateTime->lt(Carbon::now()->addDays(3))) {
                throw new Exception('Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
            }

            if ($schedule->available_seats < $totalPersons) {
                throw new Exception('Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
            }

            $this->handleUserIdentity($data, $user);

            $pricing = $this->calculatePricing($schedule, $data);

            $booking = new Booking;
            $booking->user_id = $user->id;
            $booking->tour_schedule_id = $data['schedule_id'];
            $booking->adults_count = $data['adults'];
            $booking->children_count = $data['children'];
            $booking->total_price = $pricing['finalTotalPrice'];
            $booking->discount_amount = $pricing['discountAmount'];
            $booking->coupon_id = $pricing['couponId'];
            $booking->payment_status = Booking::PAYMENT_PENDING;
            $booking->tour_status = Booking::TOUR_UPCOMING;

            $booking->payment_type = $data['payment_type'] ?? 'full';
            $booking->payment_method = $data['payment_method'] ?? 'transfer';
            $booking->paid_amount = 0;
            $booking->is_passenger_list_submitted = ($totalPersons < 2);
            $booking->save();

            $this->saveTicketBookings($pricing['selectedTickets'], $booking, $user, $schedule->departure_date);
            $this->saveBookingAddons($pricing['selectedAddons'], $booking);
            $this->saveBookingPassengers($data, $booking);

            $schedule->available_seats -= $totalPersons;
            $schedule->save();

            $this->releaseSeatHold($schedule->id, $user->id ?? $sessionId);

            broadcast(new SeatAvailabilityUpdated($schedule->id, $schedule->available_seats))->toOthers();

            return $booking;
        });
    }

    private function handleUserIdentity(array $data, User $user): void
    {
        $identity = UserIdentity::where('user_id', $user->id)->first();

        if (! $identity) {
            $identity = new UserIdentity;
            $identity->user_id = $user->id;
        } else {
            $primaryIdentityNumber = $data['passengers']['adult'][0]['identity_number'] ?? null;
            if ($primaryIdentityNumber) {
                $existingIdentity = UserIdentity::where('identity_number', $primaryIdentityNumber)
                    ->where('user_id', '!=', $user->id)
                    ->first();

                if ($existingIdentity) {
                    throw new Exception('Số CCCD/Hộ chiếu này đã được đăng ký bởi người dùng khác. Vui lòng kiểm tra lại.');
                }
            }
        }

        $primaryAdult = $data['passengers']['adult'][0] ?? null;
        if ($primaryAdult) {
            $identity->full_name = $primaryAdult['full_name'];
            $identity->identity_number = $primaryAdult['identity_number'] ?? null;
            $identity->date_of_birth = $primaryAdult['date_of_birth'];
            $identity->gender = $primaryAdult['gender'];
            $identity->issue_date = $data['issue_date'] ?? '2020-01-01';
            $identity->expiry_date = $data['expiry_date'] ?? '2040-01-01';
            $identity->issue_place = $data['issue_place'] ?? 'Hà Nội';

            if (isset($data['front_image']) && $data['front_image'] instanceof UploadedFile) {
                $frontPath = $data['front_image']->store('identities', 'public');
                $identity->front_image_url = '/storage/'.$frontPath;
            }

            if (isset($data['back_image']) && $data['back_image'] instanceof UploadedFile) {
                $backPath = $data['back_image']->store('identities', 'public');
                $identity->back_image_url = '/storage/'.$backPath;
            }

            $identity->save();
        }
    }

    private function calculatePricing(TourSchedule $schedule, array $data): array
    {
        $holidaySurcharge = Holiday::getIncreasePercentage($schedule->departure_date);

        $basePrice = $schedule->tour->base_price;
        $childPrice = $schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75);

        if ($holidaySurcharge > 0) {
            $basePrice = $basePrice * (1 + $holidaySurcharge / 100);
            $childPrice = $childPrice * (1 + $holidaySurcharge / 100);
        }

        $calculatedPrice = ($basePrice * $data['adults']) + ($childPrice * $data['children']);

        // Tickets
        $ticketPrice = 0;
        $selectedTickets = [];
        if (! empty($data['tickets']) && is_array($data['tickets'])) {
            foreach ($data['tickets'] as $ticketOptionId => $qty) {
                if ($qty > 0) {
                    $opt = TicketOption::find($ticketOptionId);
                    if ($opt) {
                        $ticketPrice += $opt->price * $qty;
                        $selectedTickets[] = [
                            'option' => $opt,
                            'qty' => $qty,
                        ];
                    }
                }
            }
        }

        // Addons
        $addonPriceTotal = 0;
        $selectedAddons = [];
        if (! empty($data['addons']) && is_array($data['addons'])) {
            foreach ($data['addons'] as $addonId => $addonData) {
                $qty = isset($addonData['qty']) ? (int) $addonData['qty'] : 0;
                if ($qty > 0) {
                    $addon = Addon::find($addonId);
                    if ($addon) {
                        $usageDate = $addonData['usage_date'] ?? $schedule->departure_date;
                        $addonSurcharge = Holiday::getIncreasePercentage($usageDate);
                        $price = $addon->price * (1 + $addonSurcharge / 100);

                        $addonPriceTotal += $price * $qty;
                        $selectedAddons[] = [
                            'addon_id' => $addon->id,
                            'addon_name' => $addon->name,
                            'price' => $price,
                            'quantity' => $qty,
                            'usage_date' => $usageDate,
                        ];
                    }
                }
            }
        }

        $finalTotalPrice = $calculatedPrice + $ticketPrice + $addonPriceTotal;
        $discountAmount = 0;
        $couponId = null;

        if (! empty($data['coupon_code'])) {
            $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();
            $coupon = Coupon::where('code', $data['coupon_code'])
                ->where(function ($query) {
                    $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                })
                ->where(function ($query) use ($tourCategoryIds) {
                    $query->whereNull('category_id')
                        ->orWhereIn('category_id', $tourCategoryIds);
                })
                ->first();

            if ($coupon && $finalTotalPrice >= $coupon->min_order_value) {
                if ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit) {
                    $discount = 0;
                    if ($coupon->discount_type === 'percent') {
                        $discount = $finalTotalPrice * ($coupon->discount_value / 100);
                        if ($coupon->max_discount) {
                            $discount = min($discount, $coupon->max_discount);
                        }
                    } else {
                        $discount = $coupon->discount_value;
                    }
                    $discountAmount = $discount;
                    $couponId = $coupon->id;
                    $finalTotalPrice = max(0, $finalTotalPrice - $discountAmount);

                    $coupon->increment('used_count');
                }
            }
        }

        return [
            'finalTotalPrice' => $finalTotalPrice,
            'discountAmount' => $discountAmount,
            'couponId' => $couponId,
            'selectedTickets' => $selectedTickets,
            'selectedAddons' => $selectedAddons,
        ];
    }

    private function saveTicketBookings(array $selectedTickets, Booking $booking, User $user, string $departureDate): void
    {
        foreach ($selectedTickets as $item) {
            $tb = new TicketBooking;
            $tb->user_id = $user->id;
            $tb->booking_id = $booking->id;
            $tb->ticket_option_id = $item['option']->id;
            $tb->quantity = $item['qty'];
            $tb->total_price = $item['option']->price * $item['qty'];
            $tb->visit_date = $departureDate;
            $tb->booking_status = 'pending';
            $tb->save();
        }
    }

    private function saveBookingAddons(array $selectedAddons, Booking $booking): void
    {
        foreach ($selectedAddons as $item) {
            BookingAddon::create([
                'booking_id' => $booking->id,
                'addon_id' => $item['addon_id'],
                'addon_name' => $item['addon_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'usage_date' => $item['usage_date'],
            ]);
        }
    }

    private function saveBookingPassengers(array $data, Booking $booking): void
    {
        if (isset($data['passengers']['adult'])) {
            foreach ($data['passengers']['adult'] as $adult) {
                $passenger = new BookingPassenger;
                $passenger->booking_id = $booking->id;
                $passenger->full_name = $adult['full_name'];
                $passenger->date_of_birth = $adult['date_of_birth'];
                $passenger->identity_number = $adult['identity_number'] ?? null;
                $passenger->gender = $adult['gender'];
                $passenger->passenger_type = 'adult';
                $passenger->save();
            }
        }

        if (isset($data['passengers']['child'])) {
            foreach ($data['passengers']['child'] as $child) {
                $passenger = new BookingPassenger;
                $passenger->booking_id = $booking->id;
                $passenger->full_name = $child['full_name'];
                $passenger->date_of_birth = $child['date_of_birth'];
                $passenger->gender = $child['gender'];
                $passenger->passenger_type = 'child';
                $passenger->save();
            }
        }
    }

    private function releaseSeatHold(int $scheduleId, $identifier): void
    {
        $holdKey = "tour_schedule_{$scheduleId}_holds";
        $currentHolds = Cache::get($holdKey, []);
        if (isset($currentHolds[$identifier])) {
            unset($currentHolds[$identifier]);
            Cache::put($holdKey, $currentHolds, now()->addMinutes(15));
        }
    }
}
