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

class ConsultationStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $consultation;
    public $student;
    public $instructor;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Consultation $consultation, User $student, User $instructor, string $status)
    {
        $this->consultation = $consultation;
        $this->student = $student;
        $this->instructor = $instructor;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' ? 'Consultation Request Approved' : 'Consultation Request Declined';
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation_status_update',
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
