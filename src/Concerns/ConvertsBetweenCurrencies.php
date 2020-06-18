<?php

namespace Makeable\LaravelCurrencies\Concerns;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract as Currency;

trait ConvertsBetweenCurrencies
{
    /**
     * @param Currency $currency
     *
     * @return Amount
     */
    public function convertTo(Currency $currency)
    {
        if ($this->currency->getCode() === $currency->getCode()) {
            return $this;
        }

        $baseAmount = $this->amount;

        if ($this->currency->getCode() !== static::baseCurrency()->getCode()) {
            $baseAmount = static::localAmountToDefault($this->amount, $this->currency);
        }

        return new static(
            static::baseAmountToLocal($baseAmount, $currency), $currency
        );
    }

    /**
     * @param $amount
     * @param Currency $currency
     *
     * @return float
     */
    protected static function baseAmountToLocal($amount, Currency $currency)
    {
        return $amount * ($currency->getExchangeRate() / 100);
    }

    /**
     * @param $amount
     * @param Currency $currency
     *
     * @return float
     */
    protected static function localAmountToDefault($amount, Currency $currency)
    {
        return $amount / ($currency->getExchangeRate() / 100);
    }
}
