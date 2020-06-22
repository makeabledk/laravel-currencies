<?php

namespace Makeable\LaravelCurrencies\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ResolvesModelCurrency
{
    public function resolveModelCurrency(string $field, array $attributes): CurrencyContract;
}
