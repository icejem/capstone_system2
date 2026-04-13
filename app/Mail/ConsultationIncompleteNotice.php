<?php

namespace App\Mail;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationIncompleteNotice extends Mailable
{
    use Queueable, SerializesModels;

    public Consultation $consultation;
    public User $student;
    public User $instructor;
    public string $recipientRole;
    public int $attempts;
    public string $reasonText;

    public function __construct(
        Consultation $consultation,
        User $student,
        User $instructor,
        string $recipientRole = 'student',
        int $attempts = 3,
        ?string $reasonText = null
    ) {
        $this->consultation = $consultation;
        $this->student = $student;
        $this->instructor = $instructor;
        $this->recipientRole = $recipientRole;
        $this->attempts = $attempts;
        $this->reasonText = trim((string) ($reasonText ?: 'because there was no answer after ' . $attempts . ' call attempts.'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consultation Marked Incomplete',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation_incomplete_notice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
