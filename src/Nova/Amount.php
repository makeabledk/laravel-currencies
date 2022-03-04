<?php

namespace Makeable\LaravelCurrencies\Nova;

use Laravel\Nova\Fields\Text;

class Amount extends Text
{
    /**
     * @param  string  $name
     * @param  string|null  $attribute
     * @return void
     */
    public function __construct($name, $attribute = null)
    {
        parent::__construct($name, $attribute);

        $this->displayUsing(function ($value) {
            return $value instanceof \Makeable\LaravelCurrencies\Amount
                ? $value->toFormat()
                : $value;
        });

        $this->resolveUsing(function ($value) {
            return $value instanceof \Makeable\LaravelCurrencies\Amount
                ? $value->get()
                : $value;
        });
    }
}
