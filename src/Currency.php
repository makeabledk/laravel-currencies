<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model implements CurrencyContract
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param $code
     * @return static
     */
    public static function fromCode($code)
    {
        return static::code($code)->firstOrFail();
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
