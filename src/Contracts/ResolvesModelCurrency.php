<?php

namespace Makeable\LaravelCurrencies\Contracts;

use Illuminate\Database\Eloquent\Model;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;

interface ResolvesModelCurrency
{
    public function resolveModelCurrency(Model $model, string $field, array $attributes): CurrencyContract;
}