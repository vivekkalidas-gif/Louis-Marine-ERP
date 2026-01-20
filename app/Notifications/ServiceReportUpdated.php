<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceReportUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $report)
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
            ->subject('Service Report Updated: #' . $this->report->id)
            ->line('Service Report #' . $this->report->id . ' for Job ' . $this->report->job->job_number . ' has been updated by ' . auth()->user()->name . '.')
            ->line('Please review the report and regenerate the invoice if necessary.')
            ->action('View Report', route('accounts.invoices.create', ['service_job_id' => $this->report->service_job_id]))
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
            'title' => 'Service Report Updated',
            'message' => 'Service Report #' . $this->report->id . ' for Job ' . $this->report->job->job_number . ' has been updated.',
            'action_url' => route('accounts.invoices.create', ['service_job_id' => $this->report->service_job_id]),
            'report_id' => $this->report->id,
            'job_number' => $this->report->job->job_number,
        ];
    }
}
