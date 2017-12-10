<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\BaseCurrency;
use Makeable\LaravelCurrencies\DefaultCurrency;
use Makeable\LaravelCurrencies\Helpers\MissingPropertiesException;
use Makeable\LaravelCurrencies\InvalidCurrencyException;
use Makeable\LaravelCurrencies\MissingBaseCurrencyException;
use Makeable\LaravelCurrencies\TestCurrency as Currency;
use Makeable\LaravelCurrencies\TestCurrency;
use Makeable\LaravelCurrencies\Tests\TestCase;

class AmountTest extends TestCase
{
    public function test_amount_requires_base_currency()
    {
        $this->unsetContainer(BaseCurrency::class);

        $this->expectException(MissingBaseCurrencyException::class);

        new Amount(100);
    }

    public function test_a_default_currency_can_be_specified()
    {
        $this->assertEquals('EUR', (new Amount(100))->currency()->getCode());

        app()->singleton(DefaultCurrency::class, function () {
            return TestCurrency::fromCode('DKK');
        });

        $this->assertEquals('DKK', Amount::defaultCurrency()->getCode());
        $this->assertEquals('DKK', (new Amount(100))->currency()->getCode());

        $this->unsetContainer(DefaultCurrency::class);
    }

    public function test_it_defaults_to_base_currency()
    {
        $this->assertEquals('EUR', $this->amount(100)->currency()->getCode());
    }

    public function test_it_throws_exception_on_invalid_currency()
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->amount(2, 'FOO');
    }

    public function test_it_has_a_zero_instantiator()
    {
        $this->amount(1); // Make sure there is a base currency
        $this->assertEquals(0, Amount::zero()->get());
    }

    public function test_it_converts_to_other_currencies()
    {
        $this->assertEquals(222, $this->amount(1500, 'DKK')->convertTo(Currency::fromCode('USD'))->get());
    }

    public function test_it_can_import_and_export()
    {
        $exported = $this->amount(50, 'DKK')->toArray();
        $imported = Amount::fromArray($exported);
        $missingAttributes = array_diff_key(array_flip(['amount', 'currency', 'formatted']), $exported);

        $this->assertEmpty($missingAttributes, 'Missing export attributes: '.implode(', ', $exported));
        $this->assertEquals(50, $imported->get());
        $this->assertEquals('DKK', $imported->currency()->getCode());
    }

    public function test_it_fails_on_invalid_import()
    {
        $this->expectException(MissingPropertiesException::class);
        Amount::fromArray(['amount' => 50]);
    }

    public function test_it_casts_to_string()
    {
        $amount = $this->amount(100);
        $this->assertEquals($amount->toFormat(), (string) $amount);
    }

    public function test_it_can_instantiate_with_cents()
    {
        $this->assertEquals(200, Amount::fromCents(20000)->get());
    }

    public function test_it_can_return_in_cents()
    {
        $this->assertEquals(20000, $this->amount(200)->toCents());
    }
}
