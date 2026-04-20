<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupUpdated
{
    use Dispatchable, SerializesModels;

    public $group;
    public $actor;
    public $changes;

    public function __construct(\App\Models\Group $group, ?Authenticatable $actor = null, array $changes = [])
    {
        $this->group = $group;
        $this->actor = $actor;
        $this->changes = $changes;
    }
}
