<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\CurrencyContract as Currency;

trait ConvertsCurrencies
{
    /**
     * @param Currency $currency
     *
     * @return Amount
     */
    public function convertTo(Currency $currency)
    {
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
    private static function baseAmountToLocal($amount, Currency $currency)
    {
        return $amount * ($currency->getExchangeRate() / 100);
    }

    /**
     * @param $amount
     * @param Currency $currency
     *
     * @return float
     */
    private static function localAmountToDefault($amount, Currency $currency)
    {
        return $amount / ($currency->getExchangeRate() / 100);
    }
}
