<?php

namespace Makeable\LaravelCurrencies\Helpers;

trait RetrievesValues
{
    /**
     * Returns a closure function to retrieve a value from an $item parameter.
     *
     * @param $value
     *
     * @return \Closure
     */
    protected static function valueRetriever($value)
    {
        if (static::useAsCallable($value)) {
            return $value;
        }

        return function ($item) use ($value) {
            return data_get($item, $value);
        };
    }

    /**
     * Determine if the given value is callable, but not a string.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected static function useAsCallable($value)
    {
        return ! is_string($value) && is_callable($value);
    }
}
