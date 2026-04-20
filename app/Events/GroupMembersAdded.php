<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupMembersAdded
{
    use Dispatchable, SerializesModels;

    public $group;
    public $addedUserIds;
    public $actor;

    public function __construct(\App\Models\Group $group, array $addedUserIds = [], ?Authenticatable $actor = null)
    {
        $this->group = $group;
        $this->addedUserIds = $addedUserIds;
        $this->actor = $actor;
    }
}
