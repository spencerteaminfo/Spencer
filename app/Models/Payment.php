<?php

namespace App\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'group_id',
        'amount_paid'
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'user_id' => 'integer',
            'group_id' => 'integer',
            'amount_paid' => 'integer',
        ];
    }

    public function getPriceTarget(): Money
    {
        return Money::of($this->event->price, $this->event->currency);
    }

    public function getPaidAmount(): Money
    {
        return Money::of($this->amount_paid, $this->event->currency);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
