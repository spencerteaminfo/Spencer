<?php

namespace App\Events\Groups\Members;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMembersRemoved
{
    use Dispatchable, SerializesModels;

    public $group;
    public $removedUserIds;
    public $actor;

    public function __construct(Group $group, array $removedUserIds = [], ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->removedUserIds = $removedUserIds;
        $this->actor = $actor;
    }
}
