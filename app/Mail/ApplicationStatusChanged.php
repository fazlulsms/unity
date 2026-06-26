<?php

namespace App\Mail;

use App\Models\MembershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MembershipApplication $application,
        public string $status,
        public ?string $remark = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'rejected'           => 'Unity Circle – Application Status Update',
            'more_info_required' => 'Unity Circle – Additional Information Required',
            'photo_required'     => 'Unity Circle – Photo Required for Your Application',
            default              => 'Unity Circle – Application Update',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-status-changed');
    }
}
