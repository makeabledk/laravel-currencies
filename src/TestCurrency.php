<?php

namespace Makeable\LaravelCurrencies;

class TestCurrency implements CurrencyContract
{
    /**
     * @var array
     */
    public static $currencies = [
        'EUR' => 100,
        'USD' => 111,
        'DKK' => 750,
    ];

    /**
     * @var
     */
    protected $code;

    /**
     * @var
     */
    protected $exchangeRate;

    /**
     * TestCurrency constructor.
     * @param $code
     * @param $exchangeRate
     */
    public function __construct($code, $exchangeRate)
    {
        $this->code = $code;
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @param $code
     *
     * @return CurrencyContract | null
     */
    public static function fromCode($code)
    {
        if (array_get(static::$currencies, $code)) {
            return new static($code, static::$currencies[$code]);
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }
}
