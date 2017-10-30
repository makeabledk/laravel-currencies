<?php

namespace Makeable\LaravelCurrencies;

class MissingBaseCurrencyException extends \Exception
{
    protected $message = 'Amount requires a base currency to run';
}
