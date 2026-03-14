<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|static create(array $attributes = [])
 */
class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'picture_url'
    ];

    protected function casts(): array
    {
        return [
            'group_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the memberships of users for this group.
     *
     * @return HasMany
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the users that are in this group.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships', 'group_id', 'user_id')
            ->withPivot('role_id')
            ->withTimestamps();
    }
}
