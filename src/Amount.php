<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract as Currency;

class Amount implements Arrayable, Castable, JsonSerializable
{
    use Helpers\RetrievesValues,
        Helpers\ValidatesArrays,
        Concerns\CalculatesAmounts,
        Concerns\ComparesAmounts,
        Concerns\ConvertsBetweenCurrencies,
        Concerns\InteractsWithCurrencies,
        Concerns\SerializesAmounts;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @param  array  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes|string
     */
    public static function castUsing(array $arguments)
    {
        return AmountCast::class;
    }

    /**
     * @param  mixed  $value
     * @param  null  $defaultCurrency
     * @return static|null
     *
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

        throw new \BadMethodCallException('Failed to parse given value as a money amount');
    }

    /**
     * @param  mixed  $amount
     * @return static
     *
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
     * @return Amount
     */
    public static function zero()
    {
        return new static(0);
    }

    /**
     * Amount constructor.
     *
     * @param  float  $amount
     * @param  CurrencyContract | mixed  $currency  null
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
     * @param  int|null  $decimals
     * @return float
     */
    public function get($decimals = null)
    {
        return round($this->amount, $decimals ?? config('currencies.calculation_decimals'));
    }

    /**
     * @return float
     */
    public function getRaw()
    {
        return $this->amount;
    }
}
