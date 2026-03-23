<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
        'group_id',
        'thumbnail_url'
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'created_at' => 'datetime',
            'deadline' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'group_id' => 'integer'
        ];
    }

    /**
     * The groups associated with this event.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    /**
     * The users individually bound/invited to this event.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * The attendance records for this event.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
