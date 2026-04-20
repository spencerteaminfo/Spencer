<?php

namespace App\Events\Users;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserProfileUpdated
{
    use Dispatchable, SerializesModels;

    public ?Authenticatable $user;
    public array $changes;

    public function __construct(?Authenticatable $user, array $changes = [])
    {
        $this->user = $user;
        $this->changes = $changes;
    }
}
