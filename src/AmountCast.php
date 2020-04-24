<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;

class AmountCast implements CastsAttributes
{
    protected static $defaultAmountField = '%s';
    protected static $defaultCurrencyField;
    protected static $defaultCurrency;

    protected $amountField;
    protected $currencyField;

    /**
     * @param  string  $amountField
     * @param  string|null  $currencyField
     */
    public static function defaultStoredAs(string $amountField, ?string $currencyField = null)
    {
        static::$defaultAmountField = $amountField;
        static::$defaultCurrencyField = $currencyField;
    }

    /**
     * @param  string|\Closure  $currency
     */
    public static function defaultModelCurrency($currency)
    {
        static::$defaultCurrency = $currency;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $attributes
     * @return mixed
     */
    protected static function getDefaultCurrency($model, $key, array $attributes)
    {
        $resolve = static::$defaultCurrency instanceof \Closure
            ? static::$defaultCurrency
            : function () {
                return static::$defaultCurrency;
            };

        return $resolve(...func_get_args());
    }

    /**
     * @param  null|string  $amountField
     * @param  null|string  $currencyField
     */
    public function __construct($amountField = null, $currencyField = null)
    {
        $this->amountField = $amountField ?? static::$defaultAmountField;
        $this->currencyField = $currencyField ?? static::$defaultCurrencyField;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Makeable\LaravelCurrencies\Amount|mixed
     * @throws \Exception
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new Amount(
            Arr::get($attributes, sprintf($this->amountField, $key)),
            $this->getModelCurrency($model, $key, $attributes)
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array|mixed
     * @throws \Exception
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_array($value)) {
            $value = Amount::fromArray($value);
        }

        if (is_numeric($value)) {
            $value = new Amount($value, $this->getModelCurrency($model, $key, $attributes));
        }

        if (! $value instanceof Amount && ! is_null($value)) {
            throw new \BadMethodCallException("Failed to cast attribute {$key} from Amount");
        }

        $storeAttributes[sprintf($this->amountField, $key)] = optional($value)->get();

        // If we're saving currency to the model, we'll only do so if there is an actual amount.
        // We won't set currency to null in case currency field is shared with other amounts.
        if ($this->currencyField !== null && $value !== null) {
            $storeAttributes[sprintf($this->currencyField, $key)] = $value->currency()->getCode();
        }

        return $storeAttributes;
    }

    /**
     * @param $model
     * @param $key
     * @param $attributes
     * @return mixed
     */
    protected function getModelCurrency($model, $key, $attributes)
    {
        $currency = Arr::get($attributes, $currencyField = sprintf($this->currencyField, $key));

        if (empty($currency)) {
            $currency = static::getDefaultCurrency($model, $key, $attributes);
        }

        return $currency;
    }
}
