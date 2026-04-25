<?php

namespace App\Mail;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationRequest extends Mailable
{
    use Queueable, SerializesModels;

    private const MAIL_TITLE = 'ONLINE FACULTY-STUDENT CONSULTATION FOR CCS';

    public $consultation;
    public $student;
    public $instructor;

    /**
     * Create a new message instance.
     */
    public function __construct(Consultation $consultation, User $student, User $instructor)
    {
        $this->consultation = $consultation;
        $this->student = $student;
        $this->instructor = $instructor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: self::MAIL_TITLE . ' - New Consultation Request from ' . $this->student->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation_request',
        );
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
