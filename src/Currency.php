<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Database\Eloquent\Model;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;

class Currency extends Model implements CurrencyContract
{
    /**
     * @var bool
     */
    public static $cacheEnabled = true;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    protected static $cachedModels;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Automatically flush cash when models updated.
     */
    public static function booted()
    {
//        static::saved(fn () => static::flushCache());
    }

    /**
     * Disable the built-in currency caching.
     */
    public static function disableCache()
    {
        static::$cacheEnabled = false;
    }

    /**
     * Flush the cached currencies.
     */
    public static function flushCache()
    {
        static::$cachedModels = null;
    }

    /**
     * @param  string  $code
     * @return Currency|null
     */
    public static function fromCode($code)
    {
        if (! static::$cacheEnabled) {
            return static::where('code', $code)->first();
        }

        $currencies = static::$cachedModels ??= static::all()->keyBy->code;

        return $currencies->get($code);
    }

    /**
     * @param  float  $amount
     * @return Amount
     */
    public function amount($amount)
    {
        return new Amount($amount, $this);
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
        return $this->exchange_rate;
    }
}
