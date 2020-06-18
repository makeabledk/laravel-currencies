<?php

namespace Makeable\LaravelCurrencies;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use Makeable\LaravelCurrencies\Contracts\CurrencyContract;
use Makeable\LaravelCurrencies\Contracts\ResolvesModelCurrency;

class AmountCast implements CastsAttributes
{
    protected static $defaultAmountField = '%s';
    protected static $defaultCurrencyField;

    protected $amountField;
    protected $currencyField;
    protected $nullable;

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
     * @param  null|string  $amountField
     * @param  null|string  $currencyField
     * @param  bool  $nullable
     */
    public function __construct($amountField = null, $currencyField = null, $nullable = null)
    {
        $currencyField = empty($currencyField) ? null : $currencyField; // convert '' to null

        $this->amountField = $amountField ?? static::$defaultAmountField;
        $this->currencyField = $currencyField ?? static::$defaultCurrencyField;
        $this->nullable = $nullable === 'false' ? false : (bool) $nullable;
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
        $value = Arr::get($attributes, sprintf($this->amountField, $key));

        if (is_null($value) && $this->nullable) {
            return;
        }

        return new Amount($value, $this->resolveModelCurrency($model, $key, $attributes));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $field
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array|mixed
     * @throws \Exception
     */
    public function set($model, string $field, $value, array $attributes)
    {
        $value = Amount::parse($value, $modelCurrency = $this->resolveModelCurrency($model, $field, $attributes));

        if ($value === null) {
            // We won't set currency field to null in case currency field is shared with other amounts.
            return [sprintf($this->amountField, $field) => null];
        }

        $actualCurrency = $value->currency();

        if ($this->currencyField === null && $actualCurrency->getCode() !== $modelCurrency->getCode()) {
            throw new \BadMethodCallException(
                "Attempted to set an amount of currency {$actualCurrency->getCode()} instead of default {$modelCurrency->getCode()}. This could lead to unexpected behavior, ".
                'as there is no currency field defined on the model '.get_class($model).". Please convert the amount to {$modelCurrency->getCode()} ".
                'before setting it, or consider introducing a currency field on the model.'
            );
        }

        $storeAttributes[sprintf($this->amountField, $field)] = $value->get();

        if ($this->currencyField !== null) {
            $storeAttributes[sprintf($this->currencyField, $field)] = $value->currency()->getCode();
        }

        return $storeAttributes;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $field
     * @param  array  $attributes
     * @return CurrencyContract
     */
    protected function resolveModelCurrency($model, $field, $attributes): CurrencyContract
    {
        if ($model instanceof ResolvesModelCurrency) {
            return $model->resolveModelCurrency($model, $field, $attributes);
        }

        $currencyCode = Arr::get($attributes, $currencyField = sprintf($this->currencyField, $field));

        return (new Amount(0, $currencyCode))->currency();
    }
}
