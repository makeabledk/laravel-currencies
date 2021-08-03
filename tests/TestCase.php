<?php

namespace Makeable\LaravelCurrencies\Tests;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Route;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\AmountCast;
use Makeable\LaravelCurrencies\CurrenciesServiceProvider;
use Makeable\LaravelCurrencies\Tests\Stubs\Product;
use Makeable\LaravelCurrencies\Tests\Stubs\ProductController;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
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
        $app->afterResolving('migrator', function (Migrator $migrator) {
            $migrator->path(__DIR__.'/migrations/');
        });

        Route::post('products', ProductController::class);

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        Amount::setBaseCurrency(TestCurrency::fromCode('EUR'));
        AmountCast::defaultStoredAs('%s');
        Product::$testCast = [];
        ProductController::$rules = [];
    }

    /**
     * @param $amount
     * @param null $currency
     * @return Amount
     * @throws \Exception
     */
    protected function amount($amount, $currency = null)
    {
        return new Amount($amount, $currency);
    }

    /**
     * @param $abstract
     * @return $this
     */
    protected function unsetContainer($abstract)
    {
        app()->bind($abstract, function () {
        });

        return $this;
    }
}
