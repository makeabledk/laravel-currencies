<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\AmountCast;
use Makeable\LaravelCurrencies\Tests\Stubs\Product;
use Makeable\LaravelCurrencies\Tests\TestCase;

class AmountCastTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_defaults_to_cast_to_amount_using_default_currency()
    {
        $product = $this->product();

        Product::$testCast = ['price_amount' => Amount::class];

        $this->assertEquals(new Amount(10, 'EUR'), Product::find($product->id)->price_amount);
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
    public function it_sets_amount_but_does_not_set_currency_on_model_by_default()
    {
        Product::$testCast = ['price_amount' => Amount::class];

        $this->assertEquals(
            new Amount(12.34, 'EUR'), // Global test currency is EUR
            Product::create(['price_amount' => new Amount(12.34, 'DKK')])->refresh()->price_amount
        );
    }

    /** @test **/
    public function it_sets_currency_when_configured_a_currency_field()
    {
        AmountCast::defaultStoredAs('%s_amount', '%s_currency');

        Product::$testCast = ['price' => Amount::class];

        $this->assertEquals(
            new Amount(12.34, 'DKK'),
            Product::create(['price' => new Amount(12.34, 'DKK')])->price
        );
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
    public function a_default_currency_may_be_resolved_per_model()
    {
        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
        AmountCast::defaultModelCurrency(function (Model $product, $key, array $attributes) {
            return 'DKK';
        });

        Product::$testCast = ['price' => Amount::class];

        $this->assertEquals(
            new Amount(12.34, 'DKK'),
            Product::create(['price' => 12.34])->price
        );
    }

    protected function product()
    {
        return Product::create([
            'price_amount' => 10,
            'price_currency' => 'DKK' // unrecognized by default
        ]);
    }
}
