<?php

namespace Makeable\LaravelCurrencies\Tests\Feature;

use Makeable\LaravelCurrencies\Tests\TestCase;

class AmountComparisonsTest extends TestCase
{
    public function test_equals()
    {
        $this->assertTrue($this->amount(100.001)->equals($this->amount(100.001)));
        $this->assertFalse($this->amount(100.001)->equals($this->amount(100)));
        $this->assertTrue($this->amount(100, 'EUR')->equals($this->amount(750, 'DKK')));
    }

    public function test_gt()
    {
        $this->assertTrue($this->amount(100.001)->gt($this->amount(100)));
        $this->assertFalse($this->amount(100, 'EUR')->gt($this->amount(750, 'DKK')));
    }

    public function test_gte()
    {
        $this->assertTrue($this->amount(100, 'EUR')->gte($this->amount(750, 'DKK')));
        $this->assertTrue($this->amount(100)->gte($this->amount(99)));
        $this->assertFalse($this->amount(100)->gte($this->amount(100.001)));
    }

    public function test_lt()
    {
        $this->assertTrue($this->amount(100)->lt($this->amount(100.001)));
        $this->assertFalse($this->amount(100, 'EUR')->gt($this->amount(750, 'DKK')));
    }

    public function test_lte()
    {
        $this->assertTrue($this->amount(100, 'EUR')->lte($this->amount(750, 'DKK')));
        $this->assertTrue($this->amount(100)->lte($this->amount(101)));
        $this->assertFalse($this->amount(100.001)->lte($this->amount(100)));
    }

    function test_is_zero()
    {
        $this->assertTrue($this->amount(0)->isZero());
        $this->assertTrue($this->amount(0.004)->isZero());
        $this->assertFalse($this->amount(0.005)->isZero());
    }
}
