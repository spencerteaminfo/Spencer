<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupDeleted
{
    use Dispatchable, SerializesModels;

    public $group;
    public $actor;

    public function __construct(\App\Models\Group $group, ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->actor = $actor;
    }
}
