<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupMembersRolesUpdated
{
    use Dispatchable, SerializesModels;

    public $group;
    public $userRoleMap;
    public $actor;

    public function __construct(\App\Models\Group $group, array $userRoleMap = [], ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->userRoleMap = $userRoleMap;
        $this->actor = $actor;
    }
}
