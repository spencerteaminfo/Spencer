<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends ReadOnlyModel
{
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'role_id', 'id');
    }

    public static function findByType(RoleType $type): ?self
    {
        return self::where('name', $type)->first();
    }
}
