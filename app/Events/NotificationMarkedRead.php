<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
+use Illuminate\Contracts\Auth\Authenticatable;

class NotificationMarkedRead
{
    use Dispatchable, SerializesModels;

    public $notification;
    public $actor;

-    public function __construct($notification, ?\App\Models\User $actor = null)
+    public function __construct($notification, ?Authenticatable $actor = null)
    {
        $this->notification = $notification;
        $this->actor = $actor;
    }
}

