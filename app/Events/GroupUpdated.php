<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class GroupUpdated
{
    use Dispatchable, SerializesModels;

    public Group $group;
    public ?Authenticatable $actor;
    public array $changes;

    public function __construct(Group $group, ?Authenticatable $actor = null, array $changes = [])
    {
        $this->group = $group;
        $this->actor = $actor;
        $this->changes = $changes;
    }
}
