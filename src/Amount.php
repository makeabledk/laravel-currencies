<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Makeable\LaravelCurrencies\CurrencyContract as Currency;

class Amount implements Arrayable, JsonSerializable
{
    use Helpers\RetrievesValues,
        Helpers\ValidatesArrays,
        Responsibilities\ComparesAmounts,
        Responsibilities\ConvertsBetweenCurrencies,
        Responsibilities\InteractsWithCurrencies,
        Responsibilities\SerializesAmounts,
        Responsibilities\TransformsAmounts;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * Amount constructor.
     *
     * @param $amount
     * @param  Currency | mixed  $currency  null
     *
     * @throws \Exception
     */
    public function __construct($amount, $currency = null)
    {
        $this->amount = $amount;
        $this->currency = static::normalizeCurrency($currency);
    }

    /**
     * @param  mixed  $amount
     * @return static
     * @throws \Exception
     */
    public static function wrap($amount)
    {
        if ($amount instanceof static) {
            return $amount;
        }

        return new static($amount);
    }

    /**
     * Use the fake currency class as implementation for test purposes.
     */
    public static function test()
    {
        static::setBaseCurrency(TestCurrency::fromCode('EUR'));
    }

    /**
     * @return Amount
     */
    public static function zero()
    {
        return new static(0);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toFormat();
    }

    /**
     * @return Currency
     */
    public function currency()
    {
        return clone $this->currency;
    }

    /**
     * @return float
     */
    public function get($decimals = 2)
    {
        return round($this->amount, $decimals);
    }

    /**
     * @return float
     */
    public function getRaw()
    {
        return $this->amount;
    }
}
