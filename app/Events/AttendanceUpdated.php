<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

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

