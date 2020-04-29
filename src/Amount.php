<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Makeable\LaravelCurrencies\CurrencyContract as Currency;

class Amount implements Arrayable, Castable, JsonSerializable
{
    use Helpers\RetrievesValues,
        Helpers\ValidatesArrays,
        Responsibilities\CalculatesAmounts,
        Responsibilities\ComparesAmounts,
        Responsibilities\ConvertsBetweenCurrencies,
        Responsibilities\InteractsWithCurrencies,
        Responsibilities\SerializesAmounts;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @return string
     */
    public static function castUsing()
    {
        return AmountCast::class;
    }

    /**
     * @param  mixed  $value
     * @param  null  $defaultCurrency
     * @return static|null
     * @throws \BadMethodCallException
     */
    public static function parse($value, $defaultCurrency = null)
    {
        if ($value === null || $value instanceof static) {
            return $value;
        }

        if (is_array($value)) {
            return self::fromArray($value);
        }

        if (is_numeric($value)) {
            return new self($value, $defaultCurrency);
        }

        throw new \BadMethodCallException('Failed to parse given value as amount');
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
