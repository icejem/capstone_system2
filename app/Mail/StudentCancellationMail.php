<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $relatedUserName;
    public $consultationDate;
    public $consultationTime;
    public $consultationEndTime;
    public $consultationType;

    public function __construct($studentName, $relatedUserName, $consultationDate, $consultationTime, $consultationEndTime, $consultationType)
    {
        $this->studentName = $studentName;
        $this->relatedUserName = $relatedUserName;
        $this->consultationDate = $consultationDate;
        $this->consultationTime = $consultationTime;
        $this->consultationEndTime = $consultationEndTime;
        $this->consultationType = $consultationType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consultation Request Cancelled - ' . $this->studentName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student_cancellation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
