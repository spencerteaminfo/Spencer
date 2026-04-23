<?php

namespace App\Events\Events;

use App\Models\Payment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentUpdated
{
    use Dispatchable, SerializesModels;

    public Payment $payment;
    public ?Authenticatable $actor;

    public function __construct(Payment $payment, ?Authenticatable $actor = null)
    {
        $this->payment = $payment;
        $this->actor = $actor;
    }
}
