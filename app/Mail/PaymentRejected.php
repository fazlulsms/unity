<?php

namespace App\Mail;

use App\Models\MonthlyFeeSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MonthlyFeeSubmission $submission) {}

    public function envelope(): Envelope
    {
        $month = date('F', mktime(0, 0, 0, $this->submission->month, 1));
        return new Envelope(subject: "Unity Circle – Payment Not Approved for {$month} {$this->submission->year}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-rejected-member');
    }
}
