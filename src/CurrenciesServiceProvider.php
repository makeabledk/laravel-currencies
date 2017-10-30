<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Support\ServiceProvider;

class CurrenciesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
    }
}
