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
        $this->booking = $booking;
        $this->schedule = $schedule;
        $this->customerName = $customerName;
        $this->customerPhone = $customerPhone;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đặt tour thành công',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour_booking',
        );
    }
}
