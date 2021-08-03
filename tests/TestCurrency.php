<?php

namespace Makeable\LaravelCurrencies\Tests;

use Makeable\LaravelCurrencies\Currency;
use Sushi\Sushi;

class TestCurrency extends Currency
{
    use Sushi;

    /**
     * @var array
     */
    public $rows = [
        [
            'code' => 'EUR',
            'exchange_rate' => 100,
        ],
        [
            'code' => 'DKK',
            'exchange_rate' => 750,
        ],
        [
            'code' => 'USD',
            'exchange_rate' => 111,
        ],
    ];
}
