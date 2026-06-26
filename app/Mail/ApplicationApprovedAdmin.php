<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public string $approvedBy,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[Unity Circle] Application Approved – ' . $this->member->user->name . ' (' . $this->member->member_number . ')');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-approved-admin');
    }
}
