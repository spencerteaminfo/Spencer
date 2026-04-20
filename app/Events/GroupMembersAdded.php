<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

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
