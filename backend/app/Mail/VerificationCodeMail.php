<?php

namespace App\Mail;

use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public VerificationCode $verification;

    public function __construct(VerificationCode $verification)
    {
        $this->verification = $verification;
    }

    public function build(): VerificationCodeMail
    {
        $code = $this->verification->code;
        return $this->subject('Your verification code')
            ->view('emails.verification_code')
            ->with(['code' => $code, 'expires' => $this->verification->expires_at]);
    }
}
