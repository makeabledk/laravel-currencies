<?php

namespace Makeable\LaravelCurrencies\Exceptions;

class MissingBaseCurrencyException extends \Exception
{
    protected $message = 'The Laravel Currencies package requires a configured base currency to run. You may specify by ie. calling Amount::setBaseCurrency("USD"); in your AppServiceProvider@boot method.';
}
