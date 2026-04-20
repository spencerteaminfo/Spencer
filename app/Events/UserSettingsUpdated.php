<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserSettingsUpdated
{
    use Dispatchable, SerializesModels;

    public $user;
    public $syncedOptionIds;
    public $previousOptionIds;

    public function __construct(?Authenticatable $user, array $syncedOptionIds = [], array $previousOptionIds = [])
    {
        $this->user = $user;
        $this->syncedOptionIds = $syncedOptionIds;
        $this->previousOptionIds = $previousOptionIds;
    }
}
