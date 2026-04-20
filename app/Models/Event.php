<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|static create(array $attributes = [])
 */

class Event extends Model
{

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'starts_at',
        'ends_at',
        'thumbnail_url'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'deadline' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_all_day' => 'boolean',
        ];
    }

    /**
     * Get the attendance of users for this group.
     *
     * @return HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Many-to-many relationship: Event may belong to multiple Groups via pivot table `event_group`.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'event_group', 'event_id', 'group_id')
            ->withTimestamps();
    }

    /**
     * Build a query for Users associated with this Event via memberships and attendances.
     * Returns a query builder so callers can further chain (->where, ->paginate, etc.).
     *
     * @return Builder
     */
    public function usersQuery(): Builder
    {
        // users -> memberships -> attendances -> event
        return User::query()
            ->join('memberships', 'users.id', '=', 'memberships.user_id')
            ->join('attendances', 'memberships.id', '=', 'attendances.membership_id')
            ->where('attendances.event_id', $this->id)
            ->select('users.*');
    }

    /**
     * Get a collection of User models associated with this Event (attendees).
     *
     * @return EloquentCollection
     */
    public function users(): EloquentCollection
    {
        return $this->usersQuery()->get();
    }
}
