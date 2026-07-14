<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $schedule;

    public $customerName;

    public $customerPhone;

    public function __construct($booking, $schedule, $customerName, $customerPhone)
    {
        // Eager load tất cả các quan hệ cần thiết để render email
        $this->booking = $booking->loadMissing([
            'booking_passengers',
            'addons',
        ]);

        $this->schedule = $schedule->loadMissing([
            'tour.tour_itineraries',
            'tour.departure_location',
            'schedule_guides.guide',
        ]);

        $this->customerName = $customerName;
        $this->customerPhone = $customerPhone;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đặt tour thành công - Mã đơn #'.str_pad($this->booking->id, 6, '0', STR_PAD_LEFT),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour_booking',
        );
    }
}
