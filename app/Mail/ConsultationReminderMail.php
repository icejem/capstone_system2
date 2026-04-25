<?php

namespace App\Mail;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    private const MAIL_TITLE = 'ONLINE FACULTY-STUDENT CONSULTATION FOR CCS';

    public Consultation $consultation;
    public User $recipient;
    public ?User $counterpart;
    public string $recipientRole;
    public int $minutesBefore;

    public function __construct(
        Consultation $consultation,
        User $recipient,
        string $recipientRole,
        ?User $counterpart,
        int $minutesBefore
    )
    {
        $this->consultation = $consultation;
        $this->recipient = $recipient;
        $this->recipientRole = $recipientRole;
        $this->counterpart = $counterpart;
        $this->minutesBefore = $minutesBefore;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: self::MAIL_TITLE . ' - Consultation Reminder - ' . $this->minutesBefore . ' Minutes Before Session',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
