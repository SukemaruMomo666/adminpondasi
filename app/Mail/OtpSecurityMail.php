<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpSecurityMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $userName;

    public function __construct($otpCode, $userName)
    {
        $this->otpCode = $otpCode;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Kode Verifikasi Keamanan Akun - Pondasikita')
                    ->view('mails.otp_security');
    }
}