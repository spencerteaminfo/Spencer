<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
+use Illuminate\Contracts\Auth\Authenticatable;

class EventUpdated
{
    use Dispatchable, SerializesModels;

    public $eventModel;
    public $actor;
    public $changes;

-    public function __construct(\App\Models\Event $eventModel, ?\App\Models\User $actor = null, array $changes = [])
+    public function __construct(\App\Models\Event $eventModel, ?Authenticatable $actor = null, array $changes = [])
    {
        $this->eventModel = $eventModel;
        $this->actor = $actor;
        $this->changes = $changes;
    }
}

