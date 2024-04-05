<?php

namespace App\Tests;



use App\Dto\CardDataDto;
use App\Exception\InvalidTransactionAmountException;
use App\Util\CommissionCalculator;
use PHPUnit\Framework\TestCase;
use App\Contracts\BinCheckerInterface;
use App\Contracts\ExchangeRatesApiInterface;
use App\Dto\ExchangeRatesDto;
use App\Dto\TransactionDataDto;

class CommissionCalculatorTest extends TestCase
{
    private $binChecker;
    private $exchangeRatesApi;

    protected function setUp(): void
    {
        $this->binChecker = $this->createMock(BinCheckerInterface::class);
        $this->exchangeRatesApi = $this->createMock(ExchangeRatesApiInterface::class);

        $exchangeRatesDto = $this->createMock(ExchangeRatesDto::class);
        $this->exchangeRatesApi->method('getExchangeRates')->willReturn($exchangeRatesDto);
        $exchangeRatesDto->method('getRate')->willReturnCallback(
            function ($currency) {
                return $currency === 'EUR' ? 1 : 1.5;  // Example exchange rate
            }
        );
    }

    public function testCalculateForEURCountryInEU()
    {
        $calculator = new CommissionCalculator($this->binChecker, $this->exchangeRatesApi);

        $transactionData = new TransactionDataDto();
        $transactionData->setAmount(100)->setCurrency('EUR')->setBin('45717360');

        $cardDataDtoMock = $this->createMock(CardDataDto::class);
        $cardDataDtoMock->method('getCountry')->willReturn('France');

        $this->binChecker->method('getCardData')->willReturn($cardDataDtoMock);

        $commission = $calculator->calculate($transactionData);
        $this->assertEquals(1.0, $commission);  // 100 EUR * 0.01 (EU multiplier)
    }

    public function testCalculateForNonEURCountryNotInEU()
    {
        $calculator = new CommissionCalculator($this->binChecker, $this->exchangeRatesApi);

        $transactionData = new TransactionDataDto();
        $transactionData->setAmount(150)->setCurrency('USD')->setBin('516793');

        $cardDataDtoMock = $this->createMock(CardDataDto::class);
        $cardDataDtoMock->method('getCountry')->willReturn('United States');

        $this->binChecker->method('getCardData')->willReturn($cardDataDtoMock);

        $commission = $calculator->calculate($transactionData);
        $this->assertEquals(2.0, $commission);
    }

    public function testCalculateForZeroAmount()
    {
        $calculator = new CommissionCalculator($this->binChecker, $this->exchangeRatesApi);

        $transactionData = new TransactionDataDto();
        $transactionData->setAmount(0)->setCurrency('EUR')->setBin('45717360');

        $cardDataDtoMock = $this->createMock(CardDataDto::class);
        $cardDataDtoMock->method('getCountry')->willReturn('France');

        $this->binChecker->method('getCardData')->willReturn($cardDataDtoMock);

        $commission = $calculator->calculate($transactionData);
        $this->assertEquals(0, $commission);
    }
    public function testCalculateForNegativeAmount()
    {
        $transactionData = new TransactionDataDto();
        $this->expectException(InvalidTransactionAmountException::class);
        $this->expectExceptionMessage('Transaction amount can\'t be negative');
        $transactionData->setAmount(-100)->setCurrency('EUR')->setBin('45717360');

    }

}
