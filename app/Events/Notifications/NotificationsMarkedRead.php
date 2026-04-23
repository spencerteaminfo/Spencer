<?php

namespace App\Events\Notifications;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationsMarkedRead
{
    use Dispatchable, SerializesModels;

    public $notifications;
    public ?Authenticatable $actor;

    public function __construct($notifications, ?Authenticatable $actor = null)
    {
        $this->notifications = $notifications;
        $this->actor = $actor;
    }
}
