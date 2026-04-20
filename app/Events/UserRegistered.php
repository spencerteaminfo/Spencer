<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public ?Authenticatable $user;
    public array $meta;

    public function __construct(?Authenticatable $user, array $meta = [])
    {
        $this->user = $user;
        $this->meta = $meta;
    }
}
