<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'attends'
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'user_id' => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
