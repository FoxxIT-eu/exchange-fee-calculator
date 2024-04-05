<?php

namespace App\Util;

use App\Contracts\BinCheckerInterface;
use App\Contracts\ExchangeRatesApiInterface;
use App\Dto\ExchangeRatesDto;
use App\Dto\TransactionDataDto;

class CommissionCalculator
{
    private const CURRENCY_CODE = 'EUR';
    private ExchangeRatesDto $exchangeData;
    public function __construct(private BinCheckerInterface $binChecker, private ExchangeRatesApiInterface $exchangeRatesApi)
    {
        $this->exchangeData = $this->exchangeRatesApi->getExchangeRates(static::CURRENCY_CODE);
    }

    public function calculate(TransactionDataDto $transactionData): float
    {
        $cardData = $this->binChecker->getCardData($transactionData->getBin());
        $rate = $this->exchangeData->getRate($transactionData->getCurrency());
        $amount = $transactionData->getAmount();

        if ($transactionData->getCurrency() != static::CURRENCY_CODE || $rate > 0) {
            $amount = $amount / $rate;
        }
        $multiplier = CountryHelper::getCountryMultiplier($cardData->getCountry());

        return $multiplier * $amount;
    }
}