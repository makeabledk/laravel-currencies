<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Support\ServiceProvider;

class CurrenciesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateCurrenciesTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_currencies_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_currencies_table.php'),
            ], 'migrations');
        }
    }
}
