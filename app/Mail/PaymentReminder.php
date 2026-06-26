<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public ?string $adminMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        $month = now()->format('F Y');
        return new Envelope(subject: "Unity Circle – Monthly Fee Reminder for {$month}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-reminder');
    }
}
