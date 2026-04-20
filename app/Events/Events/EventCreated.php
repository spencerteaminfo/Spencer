<?php

namespace App\Events\Events;

use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, SerializesModels;

    public Event $event;
    public ?Authenticatable $creator;
    public array $attendanceIds;

    public function __construct(Event $event, ?Authenticatable $creator = null, array $attendanceIds = [])
    {
        $this->event = $event;
        $this->creator = $creator;
        $this->attendanceIds = $attendanceIds;
    }
}

