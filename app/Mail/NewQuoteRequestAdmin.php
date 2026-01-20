<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewQuoteRequestAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $quoteRequest;

    public function __construct($quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    public function build()
    {
        return $this->subject('New Quote Request Received: ' . $this->quoteRequest->reference_no)
                    ->view('emails.admin.new_quote_request');
    }
}
