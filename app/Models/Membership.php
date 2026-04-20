<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'group_id'
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'role_id' => 'integer',
            'group_id' => 'integer',
        ];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(RoleType $roleType): bool
    {
        return $this->role->name === $roleType->value;
    }

    public function hasAtLeastRole(RoleType $roleType): bool
    {
        return $this->role->value >= Role::findByType($roleType)->value;
    }

    protected static function booted(): void
    {
        static::created(function (Membership $membership) {
            $events = Event::where('group_id', $membership->group_id)->get();

            foreach ($events as $event) {
                Attendance::create([
                    'event_id' => $event->id,
                    'user_id' => $membership->user_id,
                    'group_id' => $membership->group_id,
                    'attends' => false,
                ]);
            }
        });

        static::deleted(function (Membership $membership) {
            Attendance::where('user_id', $membership->user_id)->where('group_id', $membership->group_id)->delete();
        });
    }
}
