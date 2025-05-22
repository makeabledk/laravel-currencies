<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\Contracts\FeeContract;
use Makeable\LaravelCurrencies\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FeeTest extends TestCase
{
    #[Test]
    public function it_can_add_and_subtract_fees_on_an_amount()
    {
        $danishVAT = new class implements FeeContract
        {
            public function get(Amount $amount)
            {
                return $amount->multiply(0.25);
            }

            public function subtract(Amount $amount)
            {
                return $amount->multiply(0.80);
            }
        };

        $this->assertEquals(125, $this->amount(100)->add($danishVAT)->get());
        $this->assertEquals(100, $this->amount(125)->subtract($danishVAT)->get());
    }
}
