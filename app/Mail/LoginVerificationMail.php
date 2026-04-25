<?php

namespace App\Mail;

use App\Models\LoginVerification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    private const MAIL_TITLE = 'ONLINE FACULTY-STUDENT CONSULTATION FOR CCS';

    public function __construct(
        public User $user,
        public LoginVerification $verification,
        public string $verificationUrl,
        public string $denyUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: self::MAIL_TITLE . ' - Confirm your login',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login_verification',
            with: [
                'userName' => $this->user->name,
                'verificationUrl' => $this->verificationUrl,
                'expiresAt' => $this->verification->expires_at,
                'deviceLabel' => $this->verification->device_label,
                'ipAddress' => $this->verification->ip_address,
                'attemptedAt' => $this->verification->created_at,
                'denyUrl' => $this->denyUrl,
            ],
        );
    }
}
