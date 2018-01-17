<?php

namespace Makeable\LaravelCurrencies\Responsibilities;

trait SerializesAmounts
{
    /**
     * @param array $exported
     *
     * @return Amount
     *
     * @throws \Exception
     */
    public static function fromArray($exported)
    {
        static::requiresProperties(['amount', 'currency'], $exported);

        return new static($exported['amount'], static::normalizeCurrency($exported['currency']));
    }

    /**
     * @param $amount
     * @param Currency | mixed $currency null
     * @return static
     */
    public static function fromCents($amount, $currency = null)
    {
        return new static($amount / 100, $currency);
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'amount' => $this->get(),
            'currency' => $this->currency()->getCode(),
            'formatted' => $this->toFormat(),
        ];
    }

    /**
     * @return int
     */
    public function toCents()
    {
        return (int) ($this->get() * 100);
    }

    /**
     * @return string
     */
    public function toFormat()
    {
        return $this->currency()->getCode().' '.number_format($this->get(), 0, ',', '.');
    }
}
