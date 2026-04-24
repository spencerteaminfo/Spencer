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

    public function __get(string $name)
    {
        if ($name === 'event' || $name === 'groups') {
            return $this->group ?? null;
        }

        return null;
    }

    public function __isset(string $name): bool
    {
        if ($name === 'event' || $name === 'groups') {
            return isset($this->group);
        }

        return false;
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
            ->subject(__('notification.groups.new.mail.title', ['name' => $this->group->name]))
            ->line(__('notification.groups.new.mail.title', ['name' => $this->group->name]))
            ->line(__('notification.groups.new.mail.body', [
                'user' => $notifiable->first_name . ' ' . $notifiable->last_name,
                'date' => $this->group->updated_at?->format('d.m.Y')
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
            'group_id' => $this->group->id,
            'name' => $this->group->name,
            'translation' => [
                'key' => 'notification.groups.new.db',
                'params' => [
                    'name' => $this->group->name,
                    'user' => $notifiable->first_name . ' ' . $notifiable->last_name,
                ],
            ],
        ];
    }
}
