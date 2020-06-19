<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelCurrencies\Tests\TestCase;
use Makeable\LaravelCurrencies\Tests\TestCurrency;

class CurrencyTest extends TestCase
{
    /** @test **/
    public function it_caches_currencies_to_limit_db_queries()
    {
        ($connection = (new TestCurrency)->getConnection())->enableQueryLog();

        TestCurrency::flushCache();

        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('EUR');

        $this->assertCount(1, $connection->getQueryLog());
    }

    /** @test **/
    public function currency_caching_may_be_disabled()
    {
        ($connection = (new TestCurrency)->getConnection())->enableQueryLog();

        TestCurrency::flushCache();
        TestCurrency::disableCache();

        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('EUR');

        $this->assertCount(3, $connection->getQueryLog());
    }
}
