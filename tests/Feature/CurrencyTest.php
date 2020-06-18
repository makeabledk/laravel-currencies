<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelCurrencies\Currency;
use Makeable\LaravelCurrencies\Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_create_and_find_currencies()
    {
        Currency::create(['code' => 'EUR', 'exchange_rate' => 100]);

        $this->assertEquals('EUR', Currency::first()->code);
    }

    /** @test **/
    public function it_caches_currencies_to_limit_db_queries() 
    {
        Currency::flushCache();

        $queries = 0;

        DB::listen(function () use (&$queries) { $queries++; });

        Currency::fromCode('DKK');
        Currency::fromCode('DKK');
        Currency::fromCode('EUR');

        $this->assertEquals(1, $queries);
    }

    /** @test **/
    public function currency_caching_may_be_disabled()
    {
        Currency::flushCache();
        Currency::disableCache();

        $queries = 0;

        DB::listen(function () use (&$queries) { $queries++; });

        Currency::fromCode('DKK');
        Currency::fromCode('DKK');
        Currency::fromCode('EUR');

        $this->assertEquals(3, $queries);
    }
}
