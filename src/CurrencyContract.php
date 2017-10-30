<?php

namespace Makeable\LaravelCurrencies;

interface CurrencyContract
{
    /**
     * @param $code
     *
     * @return CurrencyContract
     */
    public static function fromCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return float
     */
    public function getExchangeRate();
}
