<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminActionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $actionType;
    public $actionPerformedBy;
    public $actionUserType;
    public $relatedUserName;
    public $relatedUserType;
    public $consultationDetails;
    public $actionDescription;
    public $timestamp;

    public function __construct($actionType, $actionPerformedBy, $actionUserType, $relatedUserName, $relatedUserType, $consultationDetails, $actionDescription, $timestamp = null)
    {
        $this->actionType = $actionType; // 'submitted', 'cancelled', 'approved', 'declined', 'call_started'
        $this->actionPerformedBy = $actionPerformedBy;
        $this->actionUserType = $actionUserType; // 'student' or 'instructor'
        $this->relatedUserName = $relatedUserName;
        $this->relatedUserType = $relatedUserType; // 'instructor' or 'student'
        $this->consultationDetails = $consultationDetails;
        $this->actionDescription = $actionDescription;
        $this->timestamp = $timestamp ?? now()->format('Y-m-d H:i:s');
    }

    public function envelope(): Envelope
    {
        $actionLabel = match($this->actionType) {
            'submitted' => 'New Consultation Request',
            'cancelled' => 'Consultation Cancelled',
            'approved' => 'Consultation Approved',
            'declined' => 'Consultation Declined',
            'call_started' => 'Call Started',
            default => 'Consultation Action',
        };

        return new Envelope(
            subject: '[Admin Alert] ' . $actionLabel . ' - ' . $this->actionPerformedBy,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_action',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
