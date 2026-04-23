<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\RoleType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        'price',
        'currency',
        'deadline',
        'starts_at',
        'ends_at',
        'thumbnail_url'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'description' => 'string',
            'price' => MoneyCast::class,
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
     * Get the payments of the users for this group
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
     * Build a query for Users associated with this Event via attendances.
     * Returns a query builder so callers can further chain (->where, ->paginate, etc.).
     *
     * @return Builder
     */
    public function usersQuery(): Builder
    {
        return User::query()
            ->join('attendances', 'users.id', '=', 'attendances.user_id')
            ->where('attendances.event_id', $this->id)
            ->select('users.*');
    }

    /**
     * Many-to-many relation between Event and User through the `attendances` pivot table.
     * Pivot fields include `group_id` and `attends`.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendances')->withPivot('attends');
    }

    /**
     * Return a Collection of all users "staying" for this event. This merges users from
     * direct attendances and from memberships of groups attached to this event.
     * The result is deduplicated by user id and returns User models.
     *
     * @return EloquentCollection
     */
    public function stayingUsers(): EloquentCollection
    {
        // direct users from attendances
        $direct = $this->users()->get();

        // users from group memberships of groups attached to this event
        $groupIds = $this->groups()->pluck('groups.id')->toArray();
        if (empty($groupIds)) {
            return $direct->unique('id')->values();
        }

        $memberUserIds = DB::table('memberships')->whereIn('group_id', $groupIds)->pluck('user_id')->toArray();

        if (empty($memberUserIds)) {
            return $direct->unique('id')->values();
        }

        $fromGroups = User::whereIn('id', $memberUserIds)->get();

        return $direct->merge($fromGroups)->unique('id')->values();
    }

    public function usersGroups(Authenticatable $user): Collection
    {
        $user = auth()->user();

        return $this->groups()
            ->whereDoesntHave('users', function (Builder $query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get();
    }

    public function hasUserAtLeastRole(Authenticatable $user, RoleType $roleType): bool
    {
        foreach ($this->usersGroups($user) as $group) {
            $membership = $this->getUserMembership($user, $group);

            if ($membership && $membership->hasAtLeastRole($roleType)) {
                return true;
            }
        }

        return false;
    }
}
