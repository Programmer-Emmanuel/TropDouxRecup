<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $marchand;

    public function __construct($otp, $marchand)
    {
        $this->otp = $otp;
        $this->marchand = $marchand;
    }

    public function build()
    {
        return $this
            ->subject("Votre code OTP de vérification")
            ->markdown('emails.otp');
    }
}
