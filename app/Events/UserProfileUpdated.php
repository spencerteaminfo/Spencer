<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserProfileUpdated
{
    use Dispatchable, SerializesModels;

    public $user;
    public $changes;

    public function __construct(?Authenticatable $user, array $changes = [])
    {
        $this->user = $user;
        $this->changes = $changes;
    }
}
