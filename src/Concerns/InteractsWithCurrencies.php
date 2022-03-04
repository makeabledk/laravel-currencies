<?php

namespace Makeable\LaravelCurrencies\Concerns;

use Makeable\LaravelCurrencies\Contracts\BaseCurrency;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;
use Makeable\LaravelCurrencies\Contracts\DefaultCurrency;
use Makeable\LaravelCurrencies\Currency;
use Makeable\LaravelCurrencies\Exceptions\InvalidCurrencyException;
use Makeable\LaravelCurrencies\Exceptions\MissingBaseCurrencyException;

trait InteractsWithCurrencies
{
    /**
     * "Base currency" is the currency which other currencies exchange rates
     * map to. Base currency should always have a exchange rate of 100.
     *
     * @param  CurrencyContract|string  $currency
     * @return CurrencyContract
     */
    public static function setBaseCurrency($currency)
    {
        app()->instance(BaseCurrency::class, static::normalizeCurrency($currency));

        return $currency;
    }

    /**
     * If you only want to support one currency in your app, you may
     * simply provide a 3-letter ISO code which is used to format
     * the monetary amount to to your users.
     *
     * @param  string  $code
     * @return \Makeable\LaravelCurrencies\Contracts\CurrencyContract|string
     */
    public static function setSimpleBaseCurrency(string $code)
    {
        return static::setBaseCurrency(new Currency([
            'code' => $code,
            'exchange_rate' => 100,
        ]));
    }

    /**
     * "Default currency" will be the assumed currency for any amount of
     * unspecified currency. This may differ from the base currency if
     * you wish to specify exchange rates relative to ie. USD or EUR
     * but your business is located elsewhere.
     *
     * @param  CurrencyContract|string|null  $currency
     * @return \Makeable\LaravelCurrencies\Currency
     */
    public static function setDefaultCurrency($currency)
    {
        if ($currency !== null) {
            $currency = static::normalizeCurrency($currency);
        }

        app()->instance(DefaultCurrency::class, $currency);

        return $currency;
    }

    /**
     * @return CurrencyContract
     *
     * @throws MissingBaseCurrencyException
     */
    public static function baseCurrency()
    {
        return rescue(function () {
            return static::ensureValidCurrency(app(BaseCurrency::class));
        }, function () {
            throw new MissingBaseCurrencyException();
        }, false);
    }

    /**
     * @return CurrencyContract
     */
    public static function defaultCurrency()
    {
        return rescue(function () {
            return static::ensureValidCurrency(app(DefaultCurrency::class));
        }, function () {
            return static::baseCurrency();
        }, false);
    }

    /**
     * @param  CurrencyContract|string  $currency
     * @return CurrencyContract
     *
     * @throws InvalidCurrencyException
     * @throws MissingBaseCurrencyException
     */
    protected static function normalizeCurrency($currency)
    {
        if ($currency === null) {
            $currency = static::defaultCurrency();
        }

        if (! static::validCurrency($currency)) {
            $currency = call_user_func([get_class(static::baseCurrency()), 'fromCode'], $currency);
        }

        return static::ensureValidCurrency($currency);
    }

    /**
     * @param  mixed  $currency
     * @return bool
     */
    protected static function validCurrency($currency)
    {
        return is_object($currency)
            ? $currency instanceof CurrencyContract
            : false;
    }

    /**
     * @param  mixed  $currency
     * @return CurrencyContract
     *
     * @throws InvalidCurrencyException
     */
    protected static function ensureValidCurrency($currency)
    {
        throw_unless(static::validCurrency($currency), InvalidCurrencyException::class);

        return $currency;
    }
}
