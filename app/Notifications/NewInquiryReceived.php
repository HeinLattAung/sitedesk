<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emails the site owner when someone submits the contact form.
 * Queued, so the visitor never waits on the mail server — a queue worker
 * (supervisor on the VPS) sends it in the background.
 */
class NewInquiryReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Inquiry $inquiry) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inquiry = $this->inquiry;

        return (new MailMessage)
            ->subject("New inquiry from {$inquiry->name}")
            ->replyTo($inquiry->email, $inquiry->name)
            ->line("**From:** {$inquiry->name} ({$inquiry->email})")
            ->line('**Service:** '.($inquiry->service?->title ?? 'Not specified'))
            ->line($inquiry->message)
            ->action('Open in admin', route('admin.inquiries.show', $inquiry));
    }
}
