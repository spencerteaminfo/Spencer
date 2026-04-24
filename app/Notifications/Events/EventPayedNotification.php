<?php

namespace App\Notifications\Events;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventPayedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Event $event;
    private Payment $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Payment $payment)
    {
        $this->event = $event;
        $this->payment = $payment;
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
            ->subject(__('notification.event.payment.paid.mail.title', ['name' => $this->event->title]))
            ->line(__('notification.event.payment.paid.mail.title', ['name' => $this->event->title]))
            ->action(__('notification.event.payment.paid.mail.action'), url('/event/' . $this->event->id))
            ->line(__('notification.event.payment.paid.mail.body', [
                'user' => $notifiable->first_name . ' ' . $notifiable->last_name,
                'date' => $this->payment->updated_at->format('d.m.Y')
            ]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'translation' => [
                'key' => 'notification.event.payment.paid.db',
                'params' => [
                    'name' => $this->event->title,
                    'user' => $this->payment->user->fullName(),
                ],
            ],
        ];
    }
}
