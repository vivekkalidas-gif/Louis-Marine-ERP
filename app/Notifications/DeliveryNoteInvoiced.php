<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryNoteInvoiced extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $deliveryNote, public $invoice)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice Generated: ' . $this->invoice->invoice_number)
            ->line('Invoice ' . $this->invoice->invoice_number . ' has been generated from Delivery Note ' . $this->deliveryNote->dr_number . '.')
            ->action('View Invoice', route('accounts.invoices.show', $this->invoice->id))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Invoice Generated',
            'message' => 'Invoice ' . $this->invoice->invoice_number . ' generated from DR ' . $this->deliveryNote->dr_number,
            'action_url' => route('accounts.invoices.show', $this->invoice->id),
            'invoice_id' => $this->invoice->id,
            'dr_number' => $this->deliveryNote->dr_number,
        ];
    }
}
