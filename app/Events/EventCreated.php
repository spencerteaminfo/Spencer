<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
+use Illuminate\Contracts\Auth\Authenticatable;

class EventCreated
{
    use Dispatchable, SerializesModels;

    public $eventModel;
    public $creator;
    public $attendanceIds;

-    public function __construct(\App\Models\Event $eventModel, ?\App\Models\User $creator = null, array $attendanceIds = [])
+    public function __construct(\App\Models\Event $eventModel, ?Authenticatable $creator = null, array $attendanceIds = [])
    {
        $this->eventModel = $eventModel;
        $this->creator = $creator;
        $this->attendanceIds = $attendanceIds;
    }
}

