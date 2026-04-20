<?php

namespace App\Events\Groups;

use App\Models\Group;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupCreated
{
    use Dispatchable, SerializesModels;

    public Group $group;
    public ?Authenticatable $creator;
    public array $addedUserIds;

    public function __construct(Group $group, ?Authenticatable $creator = null, array $addedUserIds = [])
    {
        $this->group = $group;
        $this->creator = $creator;
        $this->addedUserIds = $addedUserIds;
    }
}
