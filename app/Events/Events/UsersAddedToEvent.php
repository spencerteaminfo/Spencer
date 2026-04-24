<?php

namespace App\Events\Events;

use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersAddedToEvent
{
    use Dispatchable, SerializesModels;

    public Event $event;
    public ?Authenticatable $creator;
    public array $userIds;

    public function __construct(Event $event, ?Authenticatable $creator = null, array $userIds = [])
    {
        $this->event = $event;
        $this->creator = $creator;
        $this->userIds = $userIds;
    }
}
