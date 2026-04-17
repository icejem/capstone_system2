<?php

namespace App\Mail;

use App\Models\LoginVerificationToken;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class LoginVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public LoginVerificationToken $token,
        public string $ipAddress,
        public string $userAgent
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: '🔐 Confirm Your Login - Consultation Platform',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $verificationUrl = route('auth.verify-login', [
            'token' => $this->token->plain_token,
        ]);

        $browserInfo = $this->parseBrowserInfo($this->userAgent);
        $expiresIn = $this->token->expires_at->diffInMinutes(now());

        return new Content(
            view: 'emails.login-verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $verificationUrl,
                'expiresIn' => $expiresIn,
                'ipAddress' => $this->ipAddress,
                'browserInfo' => $browserInfo,
                'tokenId' => $this->token->id,
            ],
        );
    }

    /**
     * Parse browser and device information from user agent
     */
    private function parseBrowserInfo(string $userAgent): array
    {
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Simple browser detection
        if (Str::contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (Str::contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (Str::contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        } elseif (Str::contains($userAgent, 'Edge')) {
            $browser = 'Microsoft Edge';
        }

        // Simple OS detection
        if (Str::contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (Str::contains($userAgent, 'Mac')) {
            $os = 'macOS';
        } elseif (Str::contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (Str::contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (Str::contains($userAgent, 'iPhone') || Str::contains($userAgent, 'iPad')) {
            $os = 'iOS';
        }

        return [
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
