<?php

namespace Makeable\LaravelCurrencies\Contracts;

interface ResolvesModelCurrency
{
    public function resolveModelCurrency(string $field, array $attributes): CurrencyContract;
}
