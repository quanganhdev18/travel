<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\User\BookingStatusUpdatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BookingController extends Controller
{
    public function index(Request $request)
{
    Booking::updateUpcomingTourStatuses();

    /*
    |--------------------------------------------------------------------------
    | Khởi tạo truy vấn trước
    |--------------------------------------------------------------------------
    */
    $query = Booking::with([
        'user.identity',
        'tour_schedule.tour',
        'booking_passengers',
        'coupon',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Tìm kiếm
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($q) use ($search) {
            $q->where('id', $search)
                ->orWhere('pnr_code', 'like', "%{$search}%")
                ->orWhere('invoice_email', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Lọc trạng thái thanh toán
    |--------------------------------------------------------------------------
    */
    if ($request->filled('payment_status')) {
        $query->where(
            'payment_status',
            $request->payment_status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Lọc trạng thái tour
    |--------------------------------------------------------------------------
    */
    if ($request->filled('tour_status')) {
        $query->where(
            'tour_status',
            $request->tour_status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Lọc trạng thái hóa đơn
    |--------------------------------------------------------------------------
    */
    if ($request->filled('invoice_status')) {
        $query->where(
            'invoice_status',
            $request->invoice_status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Lọc đơn cần cấp vé máy bay
    |--------------------------------------------------------------------------
    */
    if (
        $request->filled('status')
        && $request->status === 'needs_flight'
    ) {
        $query
            ->where('transport_type', 'flight')
            ->whereNull('pnr_code')
            ->whereNotIn('tour_status', [
                Booking::TOUR_CANCELLED_ADMIN,
                Booking::TOUR_CANCELLED_CUSTOMER,
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Đưa yêu cầu hóa đơn lên đầu danh sách
    |--------------------------------------------------------------------------
    */
    $query->orderByRaw("
        CASE
            WHEN invoice_status = 'requested' THEN 0
            WHEN invoice_status = 'sent' THEN 1
            ELSE 2
        END
    ");

    /*
    |--------------------------------------------------------------------------
    | Các đơn mới nhất hiển thị trước
    |--------------------------------------------------------------------------
    */
    $bookings = $query
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Thống kê nhanh
    |--------------------------------------------------------------------------
    */
    $stats = [
    'total' => Booking::count(),

    'pending_payment' => Booking::where(
        'payment_status',
        Booking::PAYMENT_PENDING
    )->count(),

    'upcoming_tours' => Booking::where(
        'tour_status',
        Booking::TOUR_UPCOMING
    )->count(),

    'revenue' => Booking::where(
        'payment_status',
        Booking::PAYMENT_PAID_100
    )->sum('total_price'),

    'flight_ticket_needed' => Booking::where(
        'transport_type',
        'flight'
    )
        ->whereNull('pnr_code')
        ->whereNotIn('tour_status', [
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ])
        ->count(),

    'invoice_requested' => Booking::where(
        'invoice_status',
        'requested'
    )->count(),

    'invoice_sent' => Booking::where(
        'invoice_status',
        'sent'
    )->count(),
];

$invoiceRequestCount = $stats['invoice_requested'];
return view('admin.bookings.index', compact(
    'bookings',
    'stats',
    'invoiceRequestCount'
));

    /*
    |--------------------------------------------------------------------------
    | Số yêu cầu hóa đơn đang chờ
    |--------------------------------------------------------------------------
    */
    $invoiceRequestCount = Booking::where(
        'invoice_status',
        'requested'
    )->count();

    return view('admin.bookings.index', compact(
        'bookings',
        'stats',
        'invoiceRequestCount'
    ));
}

    /**
     * Các trạng thái tour mà admin không được phép thay đổi.
     * Sau khi tour bắt đầu, quyền điều hành thuộc về Hướng dẫn viên.
     */
    public function invoice(int $id)
{
    [$booking, $payment] = $this->getInvoiceData($id);

    return view('admin.bookings.invoice', compact(
        'booking',
        'payment'
    ));
}

public function downloadInvoice(int $id)
{

    [$booking, $payment] = $this->getInvoiceData($id);

    $invoiceCode = 'INV-'
        . $booking->created_at->format('Ymd')
        . '-'
        . str_pad($booking->id, 3, '0', STR_PAD_LEFT);

    $fileName = 'hoa-don-' . $invoiceCode . '.pdf';

    $pdf = Pdf::loadView(
        'admin.bookings.invoice-pdf',
        [
            'booking' => $booking,
            'payment' => $payment,
        ]
    );

    $pdf->setPaper('a4', 'portrait');

    $pdf->setOptions([
        'defaultFont' => 'DejaVu Sans',
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
    ]);

    return $pdf->download($fileName);
}
public function sendInvoice(int $id)
{
    [$booking, $payment] = $this->getInvoiceData($id);

    $email = $booking->invoice_email
        ?: $booking->user?->email;

    if (!$email) {
        return back()->with(
            'error',
            'Khách hàng chưa có email nhận hóa đơn.'
        );
    }

    if (($booking->invoice_status ?? 'none') !== 'requested') {
        return back()->with(
            'error',
            'Đơn này không có yêu cầu hóa đơn đang chờ xử lý.'
        );
    }

    $invoiceCode = 'INV-'
        . $booking->created_at->format('Ymd')
        . '-'
        . str_pad($booking->id, 3, '0', STR_PAD_LEFT);

    $fileName = 'hoa-don-' . $invoiceCode . '.pdf';

    try {
        $pdf = Pdf::loadView(
            'admin.bookings.invoice-pdf',
            [
                'booking' => $booking,
                'payment' => $payment,
            ]
        );

        $pdf->setPaper('a4', 'portrait');

        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        Mail::send(
            'emails.booking-invoice',
            [
                'booking' => $booking,
                'invoiceCode' => $invoiceCode,
            ],
            function ($message) use (
                $email,
                $booking,
                $pdf,
                $fileName
            ) {
                $message
                    ->to(
                        $email,
                        $booking->user?->name
                    )
                    ->subject(
                        'Hóa đơn đặt tour #' . $booking->id
                    )
                    ->attachData(
                        $pdf->output(),
                        $fileName,
                        [
                            'mime' => 'application/pdf',
                        ]
                    );
            }
        );

        $booking->invoice_status = 'sent';
        $booking->invoice_email = $email;
        $booking->invoice_sent_at = now();
        $booking->save();

        return back()->with(
            'success',
            'Đã gửi hóa đơn thành công đến ' . $email
        );
    } catch (Throwable $exception) {
        report($exception);

        return back()->with(
            'error',
            'Không gửi được hóa đơn. Vui lòng kiểm tra cấu hình email.'
        );
    }
}

private function getInvoiceData(int $id): array
{
    $booking = Booking::with([
        'user.identity',
        'tour_schedule.tour.tour_images',
        'coupon',
        'addons',
        'ticket_bookings.ticket_option.ticket',
        'booking_passengers',
        'payments',
    ])->findOrFail($id);

    $payment = $booking->payments
        ->where('payment_status', 'success')
        ->sortByDesc('paid_at')
        ->first();

    if (!$payment) {
        $payment = $booking->payments
            ->sortByDesc('created_at')
            ->first();
    }

    return [$booking, $payment];
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid_30,paid_100,failed',
            'booking_status' => 'required|in:pending,confirmed,paid,cancelled',
            'tour_status' => 'nullable|in:upcoming,in_progress,checking_in,completed,cancelled_by_customer,cancelled_by_admin',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $booking = Booking::with('tour_schedule')->findOrFail($id);

        if ($request->filled('tour_status')) {
            $validStatuses = Booking::getValidNextStatuses($booking->tour_status);
            // We allow Admin to jump statuses if needed because they are intervening manually, but let's keep the validStatuses check if we want, or remove it so Admin has FULL control.
            // The prompt says "admin can thiệp thủ công được", implying Admin should have absolute override power.
            // We'll remove the strict next-status validation so Admin can freely intervene.

            $isCurrentlyCancelled = in_array($booking->tour_status, [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);
            $willBeCancelled = in_array($request->tour_status, [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);

            if ($willBeCancelled && ! $isCurrentlyCancelled) {
                $totalPersons = $booking->adults_count + $booking->children_count;
                if ($booking->tour_schedule) {
                    $booking->tour_schedule->increment('available_seats', $totalPersons);
                }
            }

            if (! $willBeCancelled && $isCurrentlyCancelled) {
                $totalPersons = $booking->adults_count + $booking->children_count;
                if ($booking->tour_schedule) {
                    $booking->tour_schedule->decrement('available_seats', $totalPersons);
                }
            }

            $booking->tour_status = $request->tour_status;

            if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
                $booking->current_checkin_step = $request->current_checkin_step;
            } else {
                $booking->current_checkin_step = null;
            }
        }

        $booking->payment_status = $request->payment_status;
        $booking->booking_status = $request->booking_status;

        if ($booking->payment_status === Booking::PAYMENT_PAID_100) {
            $booking->paid_amount = $booking->total_price;
        } elseif ($booking->payment_status === Booking::PAYMENT_PAID_30) {
            $booking->paid_amount = $booking->total_price * 0.3;
        } elseif ($booking->payment_status === Booking::PAYMENT_FAILED || $booking->payment_status === Booking::PAYMENT_PENDING) {
            $booking->paid_amount = 0;
        }

        $oldTourStatus = $booking->getOriginal('tour_status');
        $oldPaymentStatus = $booking->getOriginal('payment_status');

        $booking->save();

        if ($booking->user) {
            if ($oldTourStatus !== $booking->tour_status) {
                $statusName = $this->getTourStatusName($booking->tour_status);
                $booking->user->notify(new BookingStatusUpdatedNotification($booking, 'Trạng thái tour của bạn đã được cập nhật thành: '.$statusName));
            } elseif ($oldPaymentStatus !== $booking->payment_status) {
                $paymentStatusName = $this->getPaymentStatusName($booking->payment_status);
                $booking->user->notify(new BookingStatusUpdatedNotification($booking, 'Trạng thái thanh toán của bạn đã được cập nhật thành: '.$paymentStatusName));
            }
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    private function getTourStatusName($status)
    {
        return match ($status) {
            Booking::TOUR_UPCOMING => 'Sắp khởi hành',
            Booking::TOUR_IN_PROGRESS => 'Đang diễn ra',
            Booking::TOUR_CHECKING_IN => 'Đang điểm danh',
            Booking::TOUR_COMPLETED => 'Đã hoàn thành',
            Booking::TOUR_CANCELLED_ADMIN => 'Đã huỷ bởi Admin',
            Booking::TOUR_CANCELLED_CUSTOMER => 'Đã huỷ bởi Khách',
            default => $status
        };
    }

    private function getPaymentStatusName($status)
    {
        return match ($status) {
            Booking::PAYMENT_PENDING => 'Chờ thanh toán',
            Booking::PAYMENT_PAID_30 => 'Đã cọc 30%',
            Booking::PAYMENT_PAID_100 => 'Đã thanh toán 100%',
            Booking::PAYMENT_FAILED => 'Thanh toán thất bại',
            default => $status
        };
    }

    public function updatePnr(Request $request, $id)
    {
        $request->validate([
            'pnr_code' => 'required|string|max:20',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->pnr_code = strtoupper($request->pnr_code);
        $booking->save();

        return back()->with('success', 'Cập nhật mã PNR thành công.');
    }

    public function liveStatuses(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return response()->json([]);
        }

        $bookings = Booking::whereIn('id', $ids)->get(['id', 'payment_status', 'booking_status', 'tour_status', 'paid_amount', 'total_price', 'cancel_reason']);

        $data = [];
        foreach ($bookings as $b) {
            $data[$b->id] = [
                'id' => $b->id,
                'payment_status' => $b->payment_status,
                'booking_status' => $b->booking_status,
                'tour_status' => $b->tour_status,
                'paid_amount' => (float) $b->paid_amount,
                'total_price' => (float) $b->total_price,
                'cancel_reason' => $b->cancel_reason,
            ];
        }

        return response()->json($data);
    }
}
