<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\TicketBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function myBookings(): View
    {
        $bookings = Booking::with([
            'tour_schedule.tour.tour_images',
            'tour_schedule.tour.primaryImage',
            'tour_schedule.tour.destination',
            'booking_passengers',
            'addons',
            'coupon',
            'payments',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Phân loại bookings
        $activeBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_UPCOMING,
            Booking::TOUR_IN_PROGRESS,
            Booking::TOUR_CHECKING_IN,
        ]);

        $pastBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_COMPLETED,
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ]);

        return view('frontend.user.bookings', compact('bookings', 'activeBookings', 'pastBookings'));
    }

    public function profile(): View
    {
        $user = Auth::user();
        $user->load(['bookings', 'wishlists', 'reviews', 'identity']);

        // Load bookings with full relations for the bookings tab
        $bookings = Booking::with([
            'tour_schedule.tour.tour_images',
            'tour_schedule.tour.primaryImage',
            'tour_schedule.tour.destination',
            'booking_passengers',
            'addons',
            'coupon',
            'payments',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $activeBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_UPCOMING,
            Booking::TOUR_IN_PROGRESS,
            Booking::TOUR_CHECKING_IN,
        ]);

        $pastBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_COMPLETED,
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ]);

        // Load ticket bookings
        $ticketBookings = TicketBooking::with([
            'ticket_option.ticket.ticket_images',
            'ticket_option.ticket.destination',
            'coupon',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Load favorites (saved tours) with tour relations for the saved tours tab
        $wishlists = Favorite::with(['tour.destination', 'tour.tour_images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.user.profile', compact('user', 'bookings', 'activeBookings', 'pastBookings', 'wishlists', 'ticketBookings'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => '/storage/'.$path]);
        }

        return redirect()->back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Cập nhật mật khẩu thành công.');
    }

    public function bookingDetail(int $id): View
    {
        $booking = Booking::with([
            'tour_schedule.tour.destination',
            'tour_schedule.tour.tour_images',
            'booking_passengers',
            'payments',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $existingReview = null;
        if ($tour = $booking->tour_schedule?->tour) {
            $existingReview = Review::where('user_id', Auth::id())
                ->where('tour_id', $tour->id)
                ->first();
        }

        return view('frontend.user.booking-detail', compact('booking', 'existingReview'));
    }

    public function cancelBooking(int $id): RedirectResponse
    {
        $booking = Booking::where('user_id', Auth::id())
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $booking->update(['booking_status' => 'cancelled']);

        return redirect()->back()->with('success', 'Hủy đơn đặt tour thành công.');
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'tour_id' => $request->tour_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return redirect()->back()->with('success', 'Gửi đánh giá chuyến đi thành công.');
    }

    public function myWishlists(): View
    {
        $wishlists = Wishlist::with(['tour.destination', 'tour.tour_images'])
            ->where('user_id', Auth::id())
            ->get();

        return view('frontend.user.wishlists', compact('wishlists'));
    }

    public function toggleWishlist(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        $userId = Auth::id();
        $tourId = (int) $request->tour_id;

        $favorite = Favorite::where('user_id', $userId)->where('tour_id', $tourId)->first();

        if ($favorite) {
            Favorite::where('user_id', $userId)->where('tour_id', $tourId)->delete();
            $added = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'tour_id' => $tourId,
            ]);
            $added = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.',
            ]);
        }

        return redirect()->back()->with(
            'success',
            $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.'
        );
    }

    public function removeWishlist(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        Favorite::where('user_id', Auth::id())
            ->where('tour_id', $request->tour_id)
            ->delete();

        return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }
}
