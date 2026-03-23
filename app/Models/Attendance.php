<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Attendance extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'group_id',
        'attends'
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'user_id' => 'integer',
            'group_id' => 'integer',
            'attends' => 'boolean',
        ];
    }

    /**
     * The event this attendance record is for.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
        User::class,
        Membership::class,
        'id',
        'id',
        'membership_id',
        'user_id'
    );
    }
}
