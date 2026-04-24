<?php

namespace App\Listeners;

use App\Events\Groups\GroupCreated;
use App\Events\Groups\Members\GroupMembersAdded;
use App\Models\User;
use App\Notifications\UserAddedToGroupNotification;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class GroupNotificationsSender
{
    public function handleEventCreated(GroupCreated $event): void
    {
        $users = User::whereIn('id', $event->addedUserIds)->get();

        Notification::send($users, new UserAddedToGroupNotification($event->group));
    }

    public function handleMembersAdded(GroupMembersAdded $event): void
    {
        $users = User::whereIn('id', $event->addedUserIds)->get();

        Notification::send($users, new UserAddedToGroupNotification($event->group));
    }

    /**
    * Register the listeners for the subscriber.
    */
    public function subscribe(Dispatcher $events): array
    {
        return [
            GroupCreated::class => 'handleEventCreated',
            GroupMembersAdded::class => 'handleMembersAdded',
        ];
    }
}
