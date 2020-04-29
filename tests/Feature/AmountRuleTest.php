<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\AmountCast;
use Makeable\LaravelCurrencies\Tests\Stubs\Product;
use Makeable\LaravelCurrencies\Tests\Stubs\ProductController;
use Makeable\LaravelCurrencies\Tests\TestCase;

class AmountRuleTest extends TestCase
{
    /** @test **/
    public function it_can_validate_amounts_using_string_rule()
    {
        ProductController::$rules = ['price' => 'required|amount'];

        $this->handleValidationExceptions();

        $error = 'The price must be a valid money amount.';

        $this->postJson('products', ['price' => 123])->assertSuccessful();
        $this->postJson('products', ['price' => 123.12])->assertSuccessful();
        $this->postJson('products', ['price' => -123])->assertSuccessful();
        $this->postJson('products', ['price' => ['amount' => 123, 'currency' => 'DKK']])->assertSuccessful();

        $this->postJson('products', ['price' => 'foo bar'])->assertJsonValidationErrors(['price' => $error]);
        $this->postJson('products', ['price' => ['amount' => 123, 'currency' => 'foo bar']])->assertJsonValidationErrors(['price' => $error]);
    }

    /** @test **/
    public function it_respects_nullable_amounts_using_string_rule()
    {
        ProductController::$rules = ['price' => 'nullable|amount'];

        $this->handleValidationExceptions();

        $this->postJson('products', ['price' => null])->assertSuccessful();
        $this->postJson('products', ['price' => 123])->assertSuccessful();
    }

    /** @test **/
    public function min_and_max_values_can_be_specified_using_the_helpers_on_rule_object()
    {
        ProductController::$rules = ['price' => ['required', (new \Makeable\LaravelCurrencies\Rules\Amount())->between(0, 1000)]];

        [$amountError, $minError, $maxError] = [
            'The price must be a valid money amount.',
            'The price must be at least 0.',
            'The price may not be greater than 1000.',
        ];

        $this->handleValidationExceptions();

        $this->postJson('products', ['price' => 0])->assertSuccessful();
        $this->postJson('products', ['price' => 0.2])->assertSuccessful();
        $this->postJson('products', ['price' => 999.99])->assertSuccessful();
        $this->postJson('products', ['price' => 1000])->assertSuccessful();
        $this->postJson('products', ['price' => 'foo bar'])->assertJsonValidationErrors(['price' => $amountError]);

        $this->postJson('products', ['price' => -0.1])->assertJsonValidationErrors(['price' => $minError]);
        $this->postJson('products', ['price' => -1])->assertJsonValidationErrors(['price' => $minError]);

        $this->postJson('products', ['price' => 1000.1])->assertJsonValidationErrors(['price' => $maxError]);
        $this->postJson('products', ['price' => 1001])->assertJsonValidationErrors(['price' => $maxError]);
    }

//
//    /** @test **/
//    public function it_defaults_to_cast_to_amount_using_default_currency()
//    {
//        $product = $this->product();
//
//        Product::$testCast = ['price_amount' => Amount::class];
//
//        $this->assertEquals(new Amount(10, 'EUR'), Product::find($product->id)->price_amount);
//    }
//
//    /** @test **/
//    public function field_names_format_may_be_specified_globally()
//    {
//        $product = $this->product();
//
//        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
//
//        Product::$testCast = ['price' => Amount::class];
//
//        $this->assertEquals(new Amount(10, 'DKK'), Product::find($product->id)->price);
//    }
//
//    /** @test **/
//    public function field_names_format_may_be_specified_per_model()
//    {
//        $product = $this->product();
//
//        AmountCast::defaultStoredAs('foo', 'bar');
//
//        Product::$testCast = ['price' => AmountCast::class.':price_amount,%s_currency']; // either hardcoded or using format
//
//        $this->assertEquals(new Amount(10, 'DKK'), Product::find($product->id)->price);
//    }
//
//    /** @test **/
//    public function it_sets_amounts_from_various_formats()
//    {
//        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
//
//        Product::$testCast = ['price' => Amount::class];
//
//        $price = new Amount(12.34, 'DKK');
//
//        $this->assertEquals($price, Product::create(['price' => $price])->price);
//        $this->assertEquals($price, Product::create(['price' => $price->toArray()])->price);
//        $this->assertEquals(new Amount(12.34, 'EUR'), Product::create(['price' => 12.34])->price);
//    }
//
//    /** @test **/
//    public function it_sets_currency_when_configured_a_currency_field()
//    {
//        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
//
//        Product::$testCast = ['price' => Amount::class];
//
//        $this->assertEquals(
//            new Amount(12.34, 'DKK'),
//            Product::create(['price' => new Amount(12.34, 'DKK')])->price
//        );
//    }
//
//    /** @test **/
//    public function it_throws_exception_when_setting_foreign_amount_when_no_currency_field_set()
//    {
//        Product::$testCast = ['price_amount' => Amount::class];
//
//        $this->expectException(\BadMethodCallException::class);
//
//        Product::create(['price_amount' => new Amount(12.34, 'DKK')]);
//    }
//
//    /** @test **/
//    public function a_default_currency_may_be_resolved_per_model()
//    {
//        AmountCast::defaultStoredAs('%s_amount', '%s_currency');
//        AmountCast::defaultModelCurrency(function (Model $product, $key, array $attributes) {
//            return 'DKK';
//        });
//
//        Product::$testCast = ['price' => Amount::class];
//
//        $this->assertEquals(
//            new Amount(12.34, 'DKK'),
//            Product::create(['price' => 12.34])->price
//        );
//    }
//
//    /** @test **/
//    public function it_converts_null_to_zero_but_this_is_configurable()
//    {
//        Product::$testCast = ['price_amount' => Amount::class];
//
//        $this->assertEquals(Amount::zero(), (new Product())->price_amount);
//
//        Product::$testCast = ['price_amount' => Amount::class.":price_amount,,true"];
//
//        $this->assertNull((new Product())->price_amount);
//    }
//
//
//    protected function product()
//    {
//        return Product::create([
//            'price_amount' => 10,
//            'price_currency' => 'DKK' // unrecognized by default
//        ]);
//    }
}
