<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupDeleted
{
    use Dispatchable, SerializesModels;

    public Group $group;
    public ?Authenticatable $actor;

    public function __construct(Group $group, ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->actor = $actor;
    }
}
