<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'avatar_url'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the memberships in groups of this user.
     *
     * @return HasMany
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function settings() {
        return $this->belongsToMany(SettingOption::class, 'user_settings', 'user_id', 'option_id')->withTimestamps();
    }

    /**
     * Relation for loading groups of this user.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'memberships', 'user_id', 'group_id');
    }

    /**
     * The events this user is individually bound to.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withTimestamps();
    }


    public function sendPasswordResetNotification($token) : void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return route('api.password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);
        });

        $this->notify(new ResetPassword($token));
    }

    public function preferredLocale(): string
    {
        return $this->resolvePreferredLocale();
    }

    public function HasLocalePreference(): string
    {
        return $this->resolvePreferredLocale();
    }

    private function resolvePreferredLocale(): string
    {
        $lang = $this->settings()
            ->whereHas('setting', fn($query) => $query->where('name', 'language'))
            ->first();

        if ($lang) {
            return match($lang->option_data) {
                'czech' => 'cz',
                'english' => 'en',
                'german' => 'de',
                default => 'en',
            };
        }
        return 'en';
    }

    public function fullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
