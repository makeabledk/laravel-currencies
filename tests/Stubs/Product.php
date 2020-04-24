<?php

namespace Makeable\LaravelCurrencies\Tests\Stubs;

class Product extends \Illuminate\Database\Eloquent\Model
{
    public static $testCast = [];

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->casts = static::$testCast;

        parent::__construct($attributes);
    }
}