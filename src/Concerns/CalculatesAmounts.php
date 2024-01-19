<?php

namespace Makeable\LaravelCurrencies\Concerns;

use BadMethodCallException;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\Contracts\FeeContract;

trait CalculatesAmounts
{
    /**
     * @param  Amount | FeeContract  $amount
     * @return Amount
     *
     * @throws \Throwable
     */
    public function add($amount)
    {
        if ($amount instanceof FeeContract) {
            $amount = $amount->get($this);
        }

        throw_unless($amount instanceof Amount, BadMethodCallException::class);

        return new static(
            $this->amount + $amount->convertTo($this->currency())->amount,
            $this->currency()
        );
    }

    /**
     * @param  $factor
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
     * @param  Amount  $amount
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
     * @param  Amount  $amount
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
     * @param  $factor
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
     * @param  $percentage
     * @return Amount
     */
    public function percent($percentage)
    {
        return new static(
            $this->amount * $percentage / 100,
            $this->currency()
        );
    }

    /**
     * @param  $decimals
     * @return Amount
     */
    public function round($decimals)
    {
        return new static(
            $this->get($decimals),
            $this->currency()
        );
    }

    /**
     * @param  Amount | FeeContract  $amount
     * @return Amount
     *
     * @throws \Throwable
     */
    public function subtract($amount)
    {
        if ($amount instanceof FeeContract) {
            return $amount->subtract($this)->convertTo($this->currency());
        }

        throw_unless($amount instanceof Amount, BadMethodCallException::class);

        return new static(
            $this->amount - $amount->convertTo($this->currency())->amount,
            $this->currency()
        );
    }

    /**
     * Retrieve the sum of an array.
     *
     * @param  $items
     * @param  null  $callback
     * @return Amount
     *
     * @throws \Exception
     */
    public static function sum($items, $callback = null)
    {
        $callback = static::valueRetriever($callback);
        $sum = null;

        foreach ($items as $item) {
            if (($amount = $callback($item)) !== null) {
                $amount = static::wrap($amount);
                $sum = $sum ? $sum->add($amount) : $amount;
            }
        }

        return static::wrap($sum);
    }
}
