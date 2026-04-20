<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupMemberRoleUpdated
{
    use Dispatchable, SerializesModels;

    public $group;
    public $userId;
    public $roleId;
    public $actor;

    public function __construct(\App\Models\Group $group, int $userId, int $roleId, ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->actor = $actor;
    }
}
