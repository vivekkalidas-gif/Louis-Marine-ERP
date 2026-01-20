<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequestReceivedCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $quoteRequest;

    public function __construct($quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    public function build()
    {
        return $this->subject('We have received your Quote Request: ' . $this->quoteRequest->reference_no)
                    ->view('emails.customer.quote_request_received');
    }
}
