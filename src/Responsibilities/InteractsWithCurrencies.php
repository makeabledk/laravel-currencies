<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

use Makeable\LaravelCurrencies\BaseCurrency;
use Makeable\LaravelCurrencies\CurrencyContract;
use Makeable\LaravelCurrencies\CurrencyContract as Currency;
use Makeable\LaravelCurrencies\DefaultCurrency;
use Makeable\LaravelCurrencies\InvalidCurrencyException;
use Makeable\LaravelCurrencies\MissingBaseCurrencyException;

trait InteractsWithCurrencies
{
    /**
     * @return CurrencyContract
     * @throws MissingBaseCurrencyException
     */
    public static function baseCurrency()
    {
        return rescue(function () {
            if (! static::validCurrency($currency = app(BaseCurrency::class))) {
                throw new \Exception();
            }

            return $currency;
        }, function () {
            throw new MissingBaseCurrencyException();
        });
    }

    /**
     * @return CurrencyContract
     * @throws MissingBaseCurrencyException
     */
    public static function defaultCurrency()
    {
        return rescue(function () {
            return app(DefaultCurrency::class);
        }, function () {
            return static::baseCurrency();
        });
    }

    /**
     * @param $currency
     * @return Currency|mixed
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

        if (! static::validCurrency($currency)) {
            throw new InvalidCurrencyException("Currency {$currency} is not a valid currency");
        }

        return $currency;
    }

    /**
     * @param $currency
     * @return bool
     */
    protected static function validCurrency($currency)
    {
        if (! is_object($currency)) {
            return false;
        }

        return in_array(Currency::class, class_implements($currency));
    }
}
