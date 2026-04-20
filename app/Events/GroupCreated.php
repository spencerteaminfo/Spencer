<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupCreated
{
    use Dispatchable, SerializesModels;

    public $group;
    public $creator;
    public $addedUserIds;

    public function __construct(\App\Models\Group $group, ?Authenticatable $creator = null, array $addedUserIds = [])
    {
        $this->group = $group;
        $this->creator = $creator;
        $this->addedUserIds = $addedUserIds;
    }
}
