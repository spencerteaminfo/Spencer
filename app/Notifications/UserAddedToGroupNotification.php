<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAddedToGroupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Group $group;

    /**
     * Create a new notification instance.
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
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
            ->subject(__('notification.groups.updated.mail.title', ['name' => $this->groups->title]))
            ->line(__('notification.groups.updated.mail.title', ['name' => $this->groups->title]))
            ->line(__('notification.groups.updated.mail.body', [
                'user' => $notifiable->first_name . ' ' . $notifiable->last_name,
                'date' => $this->event->updated_at->format('d.m.Y')
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
                'key' => 'notification.event.updated.db',
                'params' => [
                    'name' => $this->event->title,
                    'user' => $notifiable->first_name . ' ' . $notifiable->last_name,
                ],
            ],
        ];
    }
}
