<?php

namespace App\Events\Groups\Members;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMembersRolesUpdated
{
    use Dispatchable, SerializesModels;

    public $group;
    public $userRoleMap;
    public $actor;

    public function __construct(Group $group, array $userRoleMap = [], ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->userRoleMap = $userRoleMap;
        $this->actor = $actor;
    }
}
