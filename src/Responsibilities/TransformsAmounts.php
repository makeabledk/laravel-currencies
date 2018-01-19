<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

use Makeable\LaravelCurrencies\Amount;

trait TransformsAmounts
{
    /**
     * @param Amount $amount
     *
     * @return Amount
     */
    public function add(Amount $amount)
    {
        return new static(
            $this->amount + $amount->convertTo($this->currency())->amount,
            $this->currency()
        );
    }

    /**
     * @param $factor
     * @return Amount
     */
    public function divide($factor)
    {
        return new static(
            $this->amount / $factor,
            $this->currency()
        );
    }

    /**
     * @param Amount $amount
     *
     * @return Amount
     */
    public function maximum(Amount $amount)
    {
        if ($this->amount > $amount->amount) {
            return $amount;
        }

        return $this;
    }

    /**
     * @param Amount $amount
     *
     * @return Amount
     */
    public function minimum(Amount $amount)
    {
        if ($this->amount < $amount->amount) {
            return $amount;
        }

        return $this;
    }

    /**
     * @param $factor
     * @return Amount
     */
    public function multiply($factor)
    {
        return new static(
            $this->amount * $factor,
            $this->currency()
        );
    }

    /**
     * @param Amount $amount
     *
     * @return Amount
     */
    public function subtract(Amount $amount)
    {
        return new static(
            $this->amount - $amount->convertTo($this->currency())->amount,
            $this->currency()
        );
    }

    /**
     * Retrieve the sum of an array.
     *
     * @param $items
     * @param null $callback
     *
     * @return Amount
     *
     * @throws \Exception
     */
    public static function sum($items, $callback = null)
    {
        $callback = static::valueRetriever($callback);
        $sum = null;

        foreach ($items as $item) {
            $amount = $callback($item);
            $sum = $sum ? $sum->add($amount) : $amount;
        }

        return $sum ?: static::zero();
    }
}
