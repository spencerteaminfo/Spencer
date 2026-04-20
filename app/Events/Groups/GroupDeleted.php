<?php

namespace App\Events\Groups;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
