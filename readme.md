

# Laravel Currencies

[![Latest Version on Packagist](https://img.shields.io/packagist/v/makeabledk/laravel-currencies.svg?style=flat-square)](https://packagist.org/packages/makeabledk/laravel-currencies)
[![Build Status](https://img.shields.io/travis/makeabledk/laravel-currencies/master.svg?style=flat-square)](https://travis-ci.org/makeabledk/laravel-currencies)
[![StyleCI](https://styleci.io/repos/108846048/shield?branch=master)](https://styleci.io/repos/108846048)

This package provides a convenient and powerful way of interacting with currencies and amounts in Laravel.


## Install

You can install this package via composer:

``` bash
composer require makeabledk/laravel-currencies
```

On Laravel versions < 5.5, you must include the service provider in you `config/app.php`:

```php
'providers' => [
...
    /*
     * Package Service Providers...
     */
     
    \Makeable\LaravelCurrencies\CurrenciesServiceProvider::class,
]
```

After installation you mus publish and run migrations to create the `currencies` table

```bash
php artisan vendor:publish --provider="Makeable\LaravelCurrencies\CurrenciesServiceProvider"
php artisan migrate
```

## Setup

### Recommended: Seed currencies

This package provides a eloquent 'Currency' model out of the box that fetches available currencies from your database.

Often you would want to seed these currencies from your code in order to normalize them across environments. This can easily be achieved with the [Laravel Production Seeding](https://github.com/makeabledk/laravel-production-seeding) package.

Create the following seeder and let it run on each deployment:

```php
<?php

class CurrencySeeder extends \Illuminate\Database\Seeder
{
    use \Makeable\ProductionSeeding\SyncStrategy;

    /**
     * @var array
     */
    protected $currencies = [
        [
            'code' => 'EUR',
            'exchange_rate' => 100
        ], [
            'code' => 'DKK',
            'exchange_rate' => 750
        ],
        // ... 
    ];

    /**
     * Seed the currencies
     */
    public function run()
    {
        $this->apply($this->currencies, \Makeable\LaravelCurrencies\Currency::class, 'code');
        
        \Cache::flush(); // Use cache-tagging if you don't want to flush your entire cache
    }
}
```

Now your database table should look something like this:

| id | code | exchange_rate |
|----|------|---------------|
| 1  | EUR  | 100.00        |
| 2  | DKK  | 750.00        |

Note that this package requires [https://github.com/dwightwatson/rememberable]() to throttle database queries. 

Tip: If you don't want to hardcode exchange rates, create a console-command that fetches and updates from an external service, and ommit the field from the seeder.

### Register base currency (required)

The amount object requires a base currency that it uses to convert between currencies. 

The exchange rates given for your currencies must all relate to the base currency.

Define it in your `AppServiceProvider@boot`:

```php
public function boot() {
    $this->app->singleton(\Makeable\LaravelCurrencies\BaseCurrency::class, function () {
        return \Makeable\LaravelCurrencies\Currency::fromCode('EUR');
    });
}
```

### Register default currency (optional)

Additionally you have the option to define a default currency if this is not the same as your base-currency. 

You may want to have a global currency such as USD or EUR for your base-currency to perform conversions, meanwhile your application defaults to display a local currency.

This can be achieved by defining a default-currency in your `AppServiceProvider@boot` as well:

```php
public function boot() {
    // Define base currency 
    // [...]

    // Define default currency
    $this->app->singleton(\Makeable\LaravelCurrencies\BaseCurrency::class, function () {
        return \Makeable\LaravelCurrencies\Currency::fromCode('DKK');
    });
}
```

Now when instantiating an amount without an explicit Currency it will default to 'DKK':

```php
new Amount (100); // 100 DKK
Amount::zero(); // 0 DKK
```

### Example usages
Quickly create an amount
```php
new Amount(100); // EUR since that's our default
new Amount(100, Currency::fromCode('DKK')); 
new Amount(100, 'DKK'); // It automatically instantiates a currency instance given a currency-code
```

Convert between currencies
```php
$eur = new Amount(100);
$dkk = $eur->convertTo('DKK'); // 750 DKK
```

Perform simple calculations - even between currencies!
```php
$amount = new Amount(100, 'EUR');
$amount->subtract(new Amount(50)); // 50 eur
$amount->subtract(new Amount(375, 'DKK')); // 50 eur
```

Imagine you have a Product eloquent model with a @getPriceAttribute() accessor that returns an Amount object, you can even do this:
```php
$products = Product::all();
$productsTotalSum = Amount::sum($products, 'price'); 
```

Use the fluent modifiers for easy manipulation
```php
$amount = new Amount(110);

// Ensure that the amount at least a certain amount
$amount->minimum(new Amount(80)); // 110 EUR
$amount->minimum(new Amount(120)); // 120 EUR

// Ensure that the amount is no bigger than a certain amount
$amount->maximum(new Amount(80)); // 80 EUR
$amount->maximum(new Amount(750, 'DKK'); // 100 EUR (eq. 750 DKK)
```

Easily export as an array, and re-instantiate if needed. Great for serving client API*.
```php
$exported = (new Amount(100))->toArray(); // ['amount' => 100, 'currency' => 'EUR', 'formatted' => 'EUR 100']
$imported = Amount::fromArray($exported);
```
*Note it implements illuminate/support Arrayable contract, so it automatically casts to an array for eloquent models.


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

You can run the tests with:

```bash
composer test
```

## Contributing

We are happy to receive pull requests for additional functionality. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Rasmus Christoffer Nielsen](https://github.com/rasmuscnielsen)
- [All Contributors](../../contributors)

## License

Attribution-ShareAlike 4.0 International. Please see [License File](LICENSE.md) for more information.