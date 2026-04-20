<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserSettingsUpdated
{
    use Dispatchable, SerializesModels;

    public ?Authenticatable $user;
    public array $syncedOptionIds;
    public array $previousOptionIds;

    public function __construct(?Authenticatable $user, array $syncedOptionIds = [], array $previousOptionIds = [])
    {
        $this->user = $user;
        $this->syncedOptionIds = $syncedOptionIds;
        $this->previousOptionIds = $previousOptionIds;
    }
}
