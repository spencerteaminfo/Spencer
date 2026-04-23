<?php
namespace App\Services;

use App\Models\Group;
use App\Models\Membership;
use App\Enums\RoleType; // Assuming RoleType is an Enum
use Illuminate\Contracts\Auth\Authenticatable;

class MembershipService
{
    /**
     * Check if the specific user has a minimum role level within a group.
     */
    public function hasAtLeastRole(Authenticatable $user, Group $group, RoleType $roleType): bool
    {
        $membership = $this->getUserMembership($user, $group);

        return $membership && $membership->hasAtLeastRole($roleType);
    }

    /**
     * Fetch the membership record for a user in a specific group.
     */
    public function getUserMembership(Authenticatable $user, Group $group): ?Membership
    {
        return Membership::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();
    }
}
