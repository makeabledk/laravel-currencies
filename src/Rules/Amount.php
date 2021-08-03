<?php

namespace Makeable\LaravelCurrencies\Rules;

use Illuminate\Contracts\Validation\Rule;

class Amount implements Rule
{
    /**
     * @var float|null
     */
    protected $min;

    /**
     * @var float|null
     */
    protected $max;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @param  float  $min
     * @param  float  $max
     * @return \Makeable\LaravelCurrencies\Rules\Amount
     */
    public function between($min, $max)
    {
        return $this->min($min)->max($max);
    }

    /**
     * @param  float  $min
     * @return $this
     */
    public function min($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param  float  $max
     * @return $this
     */
    public function max($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            if (is_null($amount = \Makeable\LaravelCurrencies\Amount::parse($value))) {
                return false;
            }

            if ($this->min !== null && $amount->get() < $this->min) {
                $this->message = trans('validation.min.numeric', ['min' => $this->min]);

                return false;
            }

            if ($this->max !== null && $amount->get() > $this->max) {
                $this->message = trans('validation.max.numeric', ['max' => $this->max]);

                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message ?? trans('laravel-currencies::messages.amount');
    }
}
