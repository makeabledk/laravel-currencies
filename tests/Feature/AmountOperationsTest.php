<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\TestCurrency as Currency;
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
        $this->assertEquals(250, Amount::sum([$this->amount(200), $this->amount(50)])->get());
    }

    public function test_it_ignores_null_when_summing()
    {
        $this->assertEquals(250, Amount::sum([$this->amount(200), null, $this->amount(50)])->get());
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

    public function test_it_uses_all_decimals_for_calculations()
    {
        $this->assertEquals(
            2.69,
            $this->amount(1.345)->add($this->amount(1.345))->get()
        );
    }

    public function test_it_can_throttle_to_a_minimum_amount()
    {
        $this->assertEquals(0, $this->amount(-5)->minimum($this->amount(0))->get());
        $this->assertEquals(-5, $this->amount(-5)->minimum($this->amount(-10))->get());
        $this->assertEquals(5, $this->amount(5)->minimum($this->amount(0))->get());
    }

    public function test_it_can_throttle_to_a_maximum_amount()
    {
        $this->assertEquals(5.11, $this->amount(10)->maximum($this->amount(5.11))->get());
        $this->assertEquals(5, $this->amount(5)->maximum($this->amount(10))->get());
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
