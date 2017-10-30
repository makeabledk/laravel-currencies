<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

use Makeable\LaravelCurrencies\CurrencyContract as Currency;
use Makeable\LaravelCurrencies\InvalidCurrencyException;
use Makeable\LaravelCurrencies\MissingBaseCurrencyException;

trait HasBaseCurrency
{
    /**
     * @var Currency
     */
    protected static $baseCurrency;

    /**
     * @var string
     */
    protected static $defaultCurrencyImplementation = \Exception::class;

    /**
     * @param Currency|null $currency
     *
     * @return Currency|void
     */
    public static function baseCurrency(Currency $currency = null)
    {
        if ($currency === null) {
            return clone static::$baseCurrency;
        }
        static::$baseCurrency = $currency;
        static::$defaultCurrencyImplementation = get_class($currency);
    }

    /**
     * @param $currency
     * @return Currency|mixed
     * @throws InvalidCurrencyException
     * @throws MissingBaseCurrencyException
     */
    protected static function normalizeCurrency($currency)
    {
        if (! static::$baseCurrency) {
            throw new MissingBaseCurrencyException();
        }

        if ($currency === null) {
            return static::baseCurrency();
        }

        if (! $currency instanceof Currency) {
            $currency = call_user_func([static::$defaultCurrencyImplementation, 'fromCode'], $currency);
        }

        if (! $currency instanceof Currency) {
            throw new InvalidCurrencyException('Currency not found: '.$currency);
        }

        return $currency;
    }
}
