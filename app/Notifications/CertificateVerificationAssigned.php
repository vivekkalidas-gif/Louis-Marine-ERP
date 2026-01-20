<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateVerificationAssigned extends Notification
{
    use Queueable;

    public $certificate;

    /**
     * Create a new notification instance.
     */
    public function __construct($certificate)
    {
        $this->certificate = $certificate;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Certificate Assigned for Verification',
            'message' => 'You have been assigned to verify certificate ' . $this->certificate->certificate_number,
            'action_url' => route('admin.certificates.show', $this->certificate->id),
            'type' => 'info'
        ];
    }
}
