<?php

namespace App\Dto;

use App\Exception\CurrencyNotFoundException;

class ExchangeRatesDto
{
    private array $rates = [];

    public static function create(): self
    {
        return new self();
    }

    public function setRates(array $rates): self
    {
        $this->rates = $rates;
        return $this;
    }
    public function getRates(): array
    {
        return $this->rates;
    }
    public function getRate(string $currencyCode): float {
        if (!array_key_exists($currencyCode, $this->rates)) {
            throw new CurrencyNotFoundException();
        }
        return $this->rates[$currencyCode];
    }
}