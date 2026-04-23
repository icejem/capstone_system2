<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstructorCallingMail extends Mailable
{
    use Queueable, SerializesModels;

    private const MAIL_TITLE = 'CCS CONSULTATION SYSTEM';

    public $instructorName;
    public $consultationDate;
    public $consultationTime;
    public $consultationEndTime;
    public $consultationType;
    public $callAttempt;

    public function __construct($instructorName, $consultationDate, $consultationTime, $consultationEndTime, $consultationType, $callAttempt)
    {
        $this->instructorName = $instructorName;
        $this->consultationDate = $consultationDate;
        $this->consultationTime = $consultationTime;
        $this->consultationEndTime = $consultationEndTime;
        $this->consultationType = $consultationType;
        $this->callAttempt = $callAttempt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: self::MAIL_TITLE . ' - Instructor is Calling for Your Consultation - ' . $this->instructorName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instructor_calling',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
