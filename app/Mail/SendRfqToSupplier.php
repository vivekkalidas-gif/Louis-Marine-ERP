<?php

namespace App\Mail;

use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SendRfqToSupplier extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rfq;
    public $supplier;

    /**
     * Create a new message instance.
     */
    public function __construct(Rfq $rfq, Supplier $supplier)
    {
        $this->rfq = $rfq;
        $this->supplier = $supplier;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request For Quotation: ' . $this->rfq->reference_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rfq_notification', // Minimal view just for body
            with: [
                'supplierName' => $this->supplier->name,
                'rfqNumber' => $this->rfq->reference_number,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate PDF in memory
        $pdf = Pdf::loadView('storekeeper.rfq.pdf', [
            'rfq' => $this->rfq, 
            'supplier' => $this->supplier
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'RFQ_' . $this->rfq->reference_number . '.pdf'
            )
            ->withMime('application/pdf'),
        ];
    }
}
