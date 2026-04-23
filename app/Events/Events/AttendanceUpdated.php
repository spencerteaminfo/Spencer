<?php

namespace App\Events\Events;

use App\Models\Attendance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceUpdated
{
    use Dispatchable, SerializesModels;

    public Attendance $attendance;
    public ?Authenticatable $actor;

    public function __construct(Attendance $attendance, ?Authenticatable $actor = null)
    {
        $this->attendance = $attendance;
        $this->actor = $actor;
    }
}

