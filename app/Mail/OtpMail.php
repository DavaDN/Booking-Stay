<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $customerName = null)
    {
        $this->otp = $otp;
        $this->customerName = $customerName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Kode Verifikasi OTP - BookingStay')
                    ->view('emails.otp')
                    ->with([
                        'otp' => $this->otp,
                        'name' => $this->customerName,
                    ]);
    }
}
