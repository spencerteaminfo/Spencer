<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

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
