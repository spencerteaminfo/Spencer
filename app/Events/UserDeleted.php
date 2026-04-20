<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserDeleted
{
    use Dispatchable, SerializesModels;

    public ?Authenticatable $user;
    public ?Authenticatable $actor;

    public function __construct(?Authenticatable $user, ?Authenticatable $actor = null)
    {
        $this->user = $user;
        $this->actor = $actor;
    }
}
