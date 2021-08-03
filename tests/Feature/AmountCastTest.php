<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\AmountCast;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;
use Makeable\LaravelCurrencies\Contracts\ResolvesModelCurrency;
use Makeable\LaravelCurrencies\Tests\Stubs\Product;
use Makeable\LaravelCurrencies\Tests\TestCase;
use Makeable\LaravelCurrencies\Tests\TestCurrency;

class AmountCastTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_defaults_to_cast_to_amount_using_default_currency()
    {
        $product = $this->product();

        Product::$testCast = ['price_amount' => Amount::class];

        $this->assertEquals(new Amount(10, 'EUR'), Product::find($product->id)->price_amount);

        Amount::setDefaultCurrency('DKK');

        $this->assertEquals(new Amount(10, 'DKK'), Product::find($product->id)->price_amount);
    }

    /** @test **/
    public function field_names_format_may_be_specified_globally()
    {
        $product = $this->product();

        AmountCast::defaultStoredAs('%s_amount', '%s_currency');

        Product::$testCast = ['price' => Amount::class];

        $this->assertEquals(new Amount(10, 'DKK'), Product::find($product->id)->price);
    }

    /** @test **/
    public function field_names_format_may_be_specified_per_model()
    {
        $product = $this->product();

        AmountCast::defaultStoredAs('foo', 'bar');

        Product::$testCast = ['price' => AmountCast::class.':price_amount,%s_currency']; // either hardcoded or using format

        $this->assertEquals(new Amount(10, 'DKK'), Product::find($product->id)->price);
    }

    /** @test **/
    public function it_sets_amounts_from_various_formats()
    {
        AmountCast::defaultStoredAs('%s_amount', '%s_currency');

        Product::$testCast = ['price' => Amount::class];

        $price = new Amount(12.34, 'DKK');

        $this->assertEquals($price, Product::create(['price' => $price])->price);
        $this->assertEquals($price, Product::create(['price' => $price->toArray()])->price);
        $this->assertEquals(new Amount(12.34, 'EUR'), Product::create(['price' => 12.34])->price);
    }

    /** @test **/
    public function it_sets_currency_when_a_currency_field_is_configured()
    {
        AmountCast::defaultStoredAs('%s_amount', '%s_currency');

        Product::$testCast = ['price' => Amount::class];

        $this->assertEquals(
            new Amount(12.34, 'DKK'),
            Product::create(['price' => new Amount(12.34, 'DKK')])->price
        );
    }

    /** @test **/
    public function it_throws_exception_when_setting_foreign_amount_when_no_currency_field_set()
    {
        Product::$testCast = ['price_amount' => Amount::class];

        // When amount is zero, we don't mind setting another currency.
        $this->assertEquals(new Amount(0, 'EUR'), Product::create(['price_amount' => new Amount(0, 'DKK')])->price_amount);

        // Can't set DKK on an EUR field
        $this->expectException(\BadMethodCallException::class);

        Product::create(['price_amount' => new Amount(12.34, 'DKK')]);
    }

    /** @test **/
    public function currencies_may_be_custom_resolved_per_model()
    {
        $product = new class extends Product implements ResolvesModelCurrency {
            public function resolveModelCurrency(string $field, array $attributes): CurrencyContract
            {
                return TestCurrency::fromCode('USD');
            }
        };

        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
        $product::$testCast = ['price' => Amount::class];

        $this->assertEquals(
            new Amount(12.34, 'USD'),
            $product::create(['price' => 12.34])->price
        );
    }

    /** @test **/
    public function it_converts_null_to_zero_but_this_is_configurable()
    {
        Product::$testCast = ['price_amount' => Amount::class];

        $this->assertEquals(Amount::zero(), (new Product())->price_amount);

        Product::$testCast = ['price_amount' => Amount::class.':price_amount,,true'];

        $this->assertNull((new Product())->price_amount);
    }

    protected function product()
    {
        return Product::create([
            'price_amount' => 10,
            'price_currency' => 'DKK', // unrecognized by default
        ]);
    }
}
