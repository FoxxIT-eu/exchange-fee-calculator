<?php

namespace App\Contracts;

use App\Dto\ExchangeRatesDto;

interface ExchangeRatesApiInterface
{
    public function getExchangeRates(string $currencyCode): ExchangeRatesDto;
}