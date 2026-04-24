<?php

namespace App\Listeners;

use App\Events\Events\EventCreated;
use App\Events\Events\EventUpdated;
use App\Events\Events\PaymentUpdated;
use App\Events\Events\UsersAddedToEvent;
use App\Models\User;
use App\Notifications\Events\EventPayedNotification;
use App\Notifications\Events\EventUpdatedNotification;
use App\Notifications\Events\NewEventNotification;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class EventNotificationsSender
{
    public function handleEventCreated(EventCreated $event): void
    {
        $users = User::whereIn('id', $event->userIds)->get();

        Notification::send($users, new NewEventNotification($event->event));
    }

    public function handleEventUpdated(EventUpdated $event): void
    {
        $users = $event->event->stayingUsers();

        Notification::send($users, new EventUpdatedNotification($event->event));
    }

    public function handlePaymentUpdated(PaymentUpdated $event): void
    {
        $user = $event->payment->user;

        $user->notify(new EventPayedNotification($event->payment->event, $event->payment));
    }

    public function handleUsersAddedToEvent(UsersAddedToEvent $event): void
    {
        $users = User::whereIn('id', $event->userIds)->get();

        Notification::send($users, new NewEventNotification($event->event));
    }

    /**
    * Register the listeners for the subscriber.
    */
    public function subscribe(Dispatcher $events): array
    {
        return [
            EventCreated::class => 'handleEventCreated',
            EventUpdated::class => 'handleEventUpdated',
            PaymentUpdated::class => 'handlePaymentUpdated',
            UsersAddedToEvent::class => 'handleUsersAddedToEvent',
        ];
    }
}
