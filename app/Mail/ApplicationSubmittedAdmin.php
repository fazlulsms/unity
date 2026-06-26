<?php

namespace App\Mail;

use App\Models\MembershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmittedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[Unity Circle] New Membership Application – ' . $this->application->full_name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-submitted-admin');
    }
}
