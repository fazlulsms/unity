<?php

namespace App\Mail;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Receipt $receipt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Payment Receipt – {$this->receipt->receipt_number}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-receipt');
    }
}
