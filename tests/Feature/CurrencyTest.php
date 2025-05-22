<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Tests\TestCase;
use Makeable\LaravelCurrencies\Tests\TestCurrency;
use PHPUnit\Framework\Attributes\Test;

class CurrencyTest extends TestCase
{
    #[Test]
    public function it_caches_currencies_to_limit_db_queries()
    {
        ($connection = (new TestCurrency)->getConnection())->enableQueryLog();

        TestCurrency::flushCache();

        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('DKK');
        TestCurrency::fromCode('EUR');

        $this->assertCount(1, $connection->getQueryLog());
    }

    #[Test]
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
