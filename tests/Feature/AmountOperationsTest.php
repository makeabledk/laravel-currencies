<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\Tests\TestCurrency as Currency;
use Makeable\LaravelCurrencies\Tests\TestCase;

class AmountOperationsTest extends TestCase
{
    public function test_it_can_add_amounts_of_different_currencies()
    {
        $sum = $this
            ->amount(100, 'DKK')
            ->add($this->amount(200, 'DKK')->convertTo(Currency::fromCode('EUR')));

        $this->assertEquals(300, $sum->get());
    }

    public function test_it_can_subtract_amounts_of_different_currencies()
    {
        $sum = $this
            ->amount(100, 'DKK')
            ->subtract($this->amount(50, 'DKK')->convertTo(Currency::fromCode('EUR')));

        $this->assertEquals(50, $sum->get());
    }

    public function test_it_can_sum_an_array_of_amounts()
    {
        $this->assertEquals(350, Amount::sum([
            100, // Raw values will be converted to amounts of default currency
            $this->amount(200),
            $this->amount(50),
        ])->get());
    }

    public function test_it_can_sum_an_multidimensional_array_containing_amounts_using_a_key()
    {
        $sum = Amount::sum([
            ['amount' => $this->amount(200)],
            ['amount' => $this->amount(50)],
        ], 'amount');

        $this->assertEquals(250, $sum->get());
    }

    public function test_it_can_sum_an_multidimensional_array_containing_amounts_using_a_callback()
    {
        $sum = Amount::sum([
            ['amount' => $this->amount(200)],
            ['amount' => $this->amount(50)],
        ], function ($item) {
            return $item['amount'];
        });

        $this->assertEquals(250, $sum->get());
    }

    public function test_it_uses_the_currency_from_the_first_none_null_value_when_summing()
    {
        $sum = Amount::sum([
            null,
            $this->amount(50, Currency::fromCode('DKK')),
            $this->amount(100, Currency::fromCode('EUR')),
        ]);

        $this->assertEquals(800, $sum->get());
        $this->assertEquals('DKK', $sum->currency()->getCode());
        $this->assertEquals('EUR', Amount::defaultCurrency()->getCode());
    }

    public function test_it_uses_all_decimals_for_calculations()
    {
        $this->assertEquals(2.22226, $this->amount(1.11113)->add($this->amount(1.11113))->getRaw());
        $this->assertEquals(2.22226, $this->amount(1.11113)->multiply(2)->getRaw());
        $this->assertEquals(2.2223, $this->amount(1.11113)->multiply(2)->get());
    }

    public function test_it_can_cap_to_a_minimum_amount()
    {
        $this->assertEquals(0, $this->amount(-5)->minimum($this->amount(0))->get());
        $this->assertEquals(-5, $this->amount(-5)->minimum($this->amount(-10))->get());
        $this->assertEquals(5, $this->amount(5)->minimum($this->amount(0))->get());
    }

    public function test_it_can_cap_to_a_maximum_amount()
    {
        $this->assertEquals(5.11, $this->amount(10)->maximum($this->amount(5.11))->get());
        $this->assertEquals(5, $this->amount(5)->maximum($this->amount(10))->get());
    }

    public function test_it_rounds_to_decimals()
    {
        $this->assertEquals(8, $this->amount(7.5345)->round(0)->getRaw());
    }

    public function test_it_can_divide_by_numbers()
    {
        $this->assertEquals(5, $this->amount(7.5)->divide(1.5)->get());
    }

    public function test_it_can_multiply_by_numbers()
    {
        $this->assertEquals(7.5, $this->amount(5)->multiply(1.5)->get());
    }

    public function test_it_can_get_percentages()
    {
        $this->assertEquals(7.5, $this->amount(100)->percent(7.5)->get());
    }
}
