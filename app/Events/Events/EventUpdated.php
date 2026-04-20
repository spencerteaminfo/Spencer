<?php

namespace App\Events\Events;

use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventUpdated
{
    use Dispatchable, SerializesModels;

    public Event $event;
    public ?Authenticatable $actor;
    public array $changes;

    public function __construct(Event $event, ?Authenticatable $actor = null, array $changes = [])
    {
        $this->event = $event;
        $this->actor = $actor;
        $this->changes = $changes;
    }
}

