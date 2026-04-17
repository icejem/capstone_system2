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

    public function __construct(
        public User $user,
        public LoginVerification $verification,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm your login to Consultation Platform',
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
            ],
        );
    }
}
