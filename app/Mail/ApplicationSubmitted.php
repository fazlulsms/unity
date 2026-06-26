<?php

namespace App\Mail;

use App\Models\MembershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Unity Circle Application Has Been Received');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-submitted-applicant');
    }
}
