<?php

namespace Makeable\LaravelCurrencies\Tests;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\CurrenciesServiceProvider;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected function amount($amount, $currency = null)
    {
        return new Amount($amount, $currency);
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        putenv('APP_DEBUG=true');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(CurrenciesServiceProvider::class);

        return $app;
    }
}
