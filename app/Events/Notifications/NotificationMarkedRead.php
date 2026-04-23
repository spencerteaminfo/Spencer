<?php

namespace App\Events\Notifications;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationMarkedRead
{
    use Dispatchable, SerializesModels;

    public $notification;
    public $actor;

    public function __construct($notification, ?Authenticatable $actor = null)
    {
        $this->notification = $notification;
        $this->actor = $actor;
    }
}

