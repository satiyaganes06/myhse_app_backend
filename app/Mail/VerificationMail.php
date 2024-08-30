<?php

// app/Mail/VerificationMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;

    /**
     * Create a new message instance.
     *
     * @param  string  $verificationCode
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emailVerification')
            ->with(['userId' => $this->userId])
            ->subject('Email Verification');
    }
}
