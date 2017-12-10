<?php

namespace Makeable\LaravelCurrencies\Tests;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\CurrenciesServiceProvider;
use Makeable\LaravelCurrencies\DefaultCurrency;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    public function setUp()
    {
        parent::setUp();

        Amount::test();
    }

    protected function amount($amount, $currency = null)
    {
        return new Amount($amount, $currency);
    }

    protected function unsetContainer($abstract)
    {
        app()->bind($abstract, function () {
            return null;
        });

        return $this;
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
        putenv('CACHE_DRIVER=array');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(CurrenciesServiceProvider::class);

        return $app;
    }
}
