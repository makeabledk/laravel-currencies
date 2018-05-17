<?php

namespace Makeable\LaravelCurrencies;

interface FeeContract
{
    /**
     * Get fee size on given amount.
     *
     * @param Amount $amount
     * @return mixed
     */
    public function get(Amount $amount);

    /**
     * Subtract fee from given amount.
     *
     * @param Amount $amount
     * @return mixed
     */
    public function subtract(Amount $amount);
}
