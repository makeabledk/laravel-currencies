<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelCurrencies\Rules\Amount as AmountRule;

class CurrenciesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateCurrenciesTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_currencies_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_currencies_table.php'),
            ], 'migrations');
        }

        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-currencies')]);

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'laravel-currencies');

        $this->app['validator']->extend('amount', function ($attribute, $value, $parameters, $validator) {
            return (new AmountRule)->passes($attribute, $value);
        }, __('laravel-currencies::messages.amount'));
    }
}
