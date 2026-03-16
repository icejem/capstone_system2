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

    public $consultation;
    public $student;

    public function __construct(Consultation $consultation, User $student)
    {
        $this->consultation = $consultation;
        $this->student = $student;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consultation Reminder',
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
