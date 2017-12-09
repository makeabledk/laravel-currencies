<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

use Makeable\LaravelCurrencies\Amount;

trait ComparesAmounts
{
    /**
     * @param Amount $amount
     * @return bool
     */
    public function equals(Amount $amount)
    {
        return (float) $this->amount === (float) $amount->convertTo($this->currency())->amount;
    }

    /**
     * @param Amount $amount
     * @return bool
     */
    public function gt(Amount $amount)
    {
        return $this->amount > $amount->convertTo($this->currency())->amount;
    }

    /**
     * @param Amount $amount
     * @return bool
     */
    public function gte(Amount $amount)
    {
        return $this->amount >= $amount->convertTo($this->currency())->amount;
    }

    /**
     * @return bool
     */
    public function isZero()
    {
        return $this->get() === (float) 0;
    }

    /**
     * @param Amount $amount
     * @return bool
     */
    public function lt(Amount $amount)
    {
        return $this->amount < $amount->convertTo($this->currency())->amount;
    }

    /**
     * @param Amount $amount
     * @return bool
     */
    public function lte(Amount $amount)
    {
        return $this->amount <= $amount->convertTo($this->currency())->amount;
    }
}
