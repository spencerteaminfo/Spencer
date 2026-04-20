<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
+use Illuminate\Contracts\Auth\Authenticatable;

class AttendanceUpdated
{
    use Dispatchable, SerializesModels;

    public $attendance;
    public $actor;

-    public function __construct(\App\Models\Attendance $attendance, ?\App\Models\User $actor = null)
+    public function __construct(\App\Models\Attendance $attendance, ?Authenticatable $actor = null)
    {
        $this->attendance = $attendance;
        $this->actor = $actor;
    }
}

