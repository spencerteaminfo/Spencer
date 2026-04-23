<?php

namespace App\Casts;

use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return Money::ofMinor(
            $attributes[$key . '_amount'],
            $attributes[$key . '_currency']
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (!$value instanceof Money) return $value;

        return [
            $key . '_amount' => $value->getMinorAmount()->toInt(),
            $key . '_currency' => $value->getCurrency()->getCurrencyCode(),
        ];
    }
}
