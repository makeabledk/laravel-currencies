<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Currency extends Model implements CurrencyContract
{
    use Rememberable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $rememberCachePrefix = 'currency_query_';

    /**
     * @var string
     */
    protected $rememberCacheTag = 'currencies';

    /**
     * Always cache queries. Cache is flushed on update by production-seeder
     * @var int
     */
    protected $rememberFor = 60*24*365;

    /**
     * @param $code
     * @return Currency
     * @throws InvalidCurrencyException
     */
    public static function fromCode($code)
    {
        return static::code($code)->first();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $query
     * @param $code
     * @return mixed
     */
    public function scopeCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $amount
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
