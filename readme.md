

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

After installation run migrations to create the `currencies` table

```
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
    }
}
```

Now your database table should look something like this:

| id | code | exchange_rate |
|----|------|---------------|
| 1  | EUR  | 100.00        |
| 2  | DKK  | 750.00        |

Tip: If you don't want to hardcode exchange rates, create a console-command that fetches and updates from an external service, and ommit the field from the seeder.

### Register base currency

The amount object requires a base currency that it uses to convert between currencies. 

Define it in your `AppServiceProvider@boot`:

```php
public function boot() {
    Amount::baseCurrency(Currency::fromCode('EUR'));
}
```


### Example usages
Quickly create an amount
```php
new Amount(100); // EUR since that's our default
new Amount(100, Currency::fromCode('DKK')); 
```

Convert between currencies
```php
$eur = new Amount(100);
$dkk = $eur->convertTo(Currency::fromCode('DKK')); // 750 
```

Perform simple calculations - even between currencies!
```php
$amount = new Amount(100, Currency::fromCode('EUR'));
$amount->subtract(new Amount(50)); // 50 eur
$amount->subtract(new Amount(375, Currency::fromCode('DKK'))); // 50 eur
```

Given you have a Product eloquent model with a @getPriceAttribute() accessor that returns an Amount object, you can even do this:
```php
$products = Product::all();
$productsTotalSum = Amount::sum($products, 'price'); 
```

Use the fluent modifiers for easy manipulation
```php
$amount = new Amount(110);
$amount->minimum(new Amount(80)); // 110 EUR
$amount->minimum(new Amount(120)); // 120 EUR
$amount->maximum(new Amount(80)); // 80 EUR
$amount->maximum(new Amount(750, Currency::fromCode('DKK')); // 100 EUR (eq. 750 DKK)
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