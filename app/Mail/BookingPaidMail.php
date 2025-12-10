<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $rooms;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $rooms = [])
    {
        $this->booking = $booking;
        $this->rooms = $rooms;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Paid Booking: ' . ($this->booking->booking_code ?? ''))
                    ->view('emails.booking-paid')
                    ->with([
                        'booking' => $this->booking,
                        'rooms' => $this->rooms,
                    ]);
    }
}
