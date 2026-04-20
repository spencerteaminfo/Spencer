<?php

namespace App\Events\Groups\Members;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMembersAdded
{
    use Dispatchable, SerializesModels;

    public Group $group;
    public array $addedUserIds;
    public ?Authenticatable $actor;

    public function __construct(Group $group, array $addedUserIds = [], ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->addedUserIds = $addedUserIds;
        $this->actor = $actor;
    }
}
