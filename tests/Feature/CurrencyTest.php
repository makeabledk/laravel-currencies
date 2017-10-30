<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelCurrencies\Currency;
use Makeable\LaravelCurrencies\Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    function it_can_create_and_find_currencies()
    {
        Currency::create(['code' => 'EUR', 'exchange_rate' => 100]);

        $this->assertEquals('EUR', Currency::first()->code);
    }
}