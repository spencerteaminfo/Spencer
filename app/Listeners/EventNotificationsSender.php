<?php

namespace App\Listeners;

use App\Events\Events\EventCreated;
use App\Notifications\Events\EventUpdatedNotification;
use Illuminate\Events\Dispatcher;

class EventNotificationsSender
{
    /**
    * Handle event creation.
    */
    public function handleEventCreated(EventCreated $event): void
    {
        $eventModel = $event->event;
        $event->creator->notify(new EventUpdatedNotification($eventModel));
    }

    /**
    * Register the listeners for the subscriber.
    */
    public function subscribe(Dispatcher $events): array
    {
        return [
            EventCreated::class => 'handleEventCreated',
        ];
    }
}
