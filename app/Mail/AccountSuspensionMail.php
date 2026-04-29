<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AccountSuspensionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reason,
        public ?string $remainingLabel = null,
        public ?Carbon $expiresAt = null
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Suspension Notice',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account_suspension',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

