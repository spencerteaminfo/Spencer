<?php

namespace App\Events\Groups\Members;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMemberRoleUpdated
{
    use Dispatchable, SerializesModels;

    public Group $group;
    public int $userId;
    public int $roleId;
    public ?Authenticatable $actor;

    public function __construct(Group $group, int $userId, int $roleId, ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->actor = $actor;
    }
}
