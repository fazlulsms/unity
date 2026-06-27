<?php

namespace App\Mail;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
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

    public function attachments(): array
    {
        try {
            $pdf = Pdf::loadView('receipts.receipt-pdf', ['receipt' => $this->receipt])->output();
            $filename = 'Receipt-' . $this->receipt->receipt_number . '.pdf';

            return [
                Attachment::fromData(fn() => $pdf, $filename)->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            logger()->error('Receipt PDF attachment failed: ' . $e->getMessage());
            return [];
        }
    }
}
