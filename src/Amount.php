<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Support\Arrayable;
use Makeable\LaravelCurrencies\CurrencyContract as Currency;

class Amount implements Arrayable
{
    use Helpers\RetrievesValues,
        Helpers\ValidatesArrays,
        Responsibilities\ComparesAmounts,
        Responsibilities\ConvertsCurrencies,
        Responsibilities\HasBaseCurrency,
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
     * @param Currency | mixed $currency null
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
     * @param array $exported
     *
     * @return Amount
     *
     * @throws \Exception
     */
    public static function fromArray($exported)
    {
        static::requiresProperties(['amount', 'currency'], $exported);

        return new static($exported['amount'], static::normalizeCurrency($exported['currency']));
    }

    /**
     * @param $amount
     * @param Currency | mixed $currency null
     * @return static
     */
    public static function fromCents($amount, $currency = null)
    {
        return new static($amount / 100, $currency);
    }

    /**
     * @return float
     */
    public function get()
    {
        return round($this->amount, 2);
    }

    /**
     * Use the fake currency class as implementation for test purposes.
     */
    public static function test()
    {
        static::baseCurrency(TestCurrency::fromCode('EUR'));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'amount' => $this->get(),
            'currency' => $this->currency()->getCode(),
            'formatted' => $this->toFormat(),
        ];
    }

    /**
     * @return float
     */
    public function toCents()
    {
        return $this->get() * 100;
    }

    /**
     * @return string
     */
    public function toFormat()
    {
        return $this->currency()->getCode().' '.number_format($this->get(), 0, ',', '.');
    }

    /**
     * @return Amount
     */
    public static function zero()
    {
        return new static(0);
    }
}
