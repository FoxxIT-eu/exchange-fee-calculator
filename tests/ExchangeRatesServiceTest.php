<?php

namespace App\Tests;

use App\Services\ExchangeRatesService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRatesServiceTest extends TestCase
{
    private $client;
    private $response;

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testGetExchangeRatesSuccess()
    {
        $currencyCode = 'EUR';
        $apiResponse = [
            'result' => 'success',
            'base_code' => $currencyCode,
            'conversion_rates' => [
                "EUR" => 1,
                "AED" => 3.9833,
                "AFN" => 76.7159,
                "ALL" => 102.5571,
                "AMD" => 421.9808,
                "ANG" => 1.9415,
                "AOA" => 917.1423,
                "ARS" => 934.6701,
                "AUD" => 1.6446,
                "AWG" => 1.9415,
                "AZN" => 1.8438,
                "BAM" => 1.9558,
                "BBD" => 2.1692,
                "BDT" => 119.0002,
                "BGN" => 1.9558,
                "BHD" => 0.4078,
                "BIF" => 3089.9568,
                "BMD" => 1.0846,
                "BND" => 1.4622,
                "BOB" => 7.4901,
                "BRL" => 5.4768,
                "BSD" => 1.0846,
                "BTN" => 90.4846,
                "BWP" => 14.8608,
                "BYN" => 3.5435,
                "BZD" => 2.1692,
                "CAD" => 1.4669,
                "CDF" => 2983.8559,
                "CHF" => 0.9794,
                "CLP" => 1038.2601,
                "CNY" => 7.8538,
                "COP" => 4123.847,
                "CRC" => 544.5448,
                "CUP" => 26.0308,
                "CVE" => 110.265,
                "CZK" => 25.3025,
                "DJF" => 192.7595,
                "DKK" => 7.4602,
                "DOP" => 64.1498,
                "DZD" => 145.7265,
                "EGP" => 51.3484,
                "ERN" => 16.2693,
                "ETB" => 61.5147,
                "FJD" => 2.4309,
                "FKP" => 0.8573,
                "FOK" => 7.4599,
                "GBP" => 0.8573,
                "GEL" => 2.9086,
                "GGP" => 0.8573,
                "GHS" => 14.6087,
                "GIP" => 0.8573,
                "GMD" => 70.0562,
                "GNF" => 9311.9905,
                "GTQ" => 8.432,
                "GYD" => 225.4649,
                "HKD" => 8.4931,
                "HNL" => 26.7104,
                "HRK" => 7.5345,
                "HTG" => 142.8236,
                "HUF" => 391.9552,
                "IDR" => 17244.5255,
                "ILS" => 4.0469,
                "IMP" => 0.8573,
                "INR" => 90.4936,
                "IQD" => 1409.3957,
                "IRR" => 45748.9946,
                "ISK" => 150.3353,
                "JEP" => 0.8573,
                "JMD" => 166.0154,
                "JOD" => 0.769,
                "JPY" => 164.2753,
                "KES" => 141.352,
                "KGS" => 96.8917,
                "KHR" => 4358,
                "KID" => 1.6447,
                "KMF" => 491.9678,
                "KRW" => 1463.786,
                "KWD" => 0.3336,
                "KYD" => 0.9038,
                "KZT" => 483.943,
                "LAK" => 22741.2147,
                "LBP" => 97073.363,
                "LKR" => 324.7643,
                "LRD" => 209.3535,
                "LSL" => 20.273,
                "LYD" => 5.229,
                "MAD" => 10.9117,
                "MDL" => 19.1924,
                "MGA" => 4658.8895,
                "MKD" => 61.495,
                "MMK" => 2854.7855,
                "MNT" => 3663.8449,
                "MOP" => 8.7482,
                "MRU" => 42.9917,
                "MUR" => 49.8173,
                "MVR" => 16.7163,
                "MWK" => 1875.1167,
                "MXN" => 17.9666,
                "MYR" => 5.1427,
                "MZN" => 69.3176,
                "NAD" => 20.273,
                "NGN" => 1444.9104,
                "NIO" => 39.8448,
                "NOK" => 11.6237,
                "NPR" => 144.7754,
                "NZD" => 1.798,
                "OMR" => 0.417,
                "PAB" => 1.0846,
                "PEN" => 3.9986,
                "PGK" => 4.0963,
                "PHP" => 61.2232,
                "PKR" => 301.389,
                "PLN" => 4.2914,
                "PYG" => 7965.2585,
                "QAR" => 3.948,
                "RON" => 4.969,
                "RSD" => 117.15,
                "RUB" => 100.1332,
                "RWF" => 1441.533,
                "SAR" => 4.0673,
                "SBD" => 9.1779,
                "SCR" => 14.7641,
                "SDG" => 481.407,
                "SEK" => 11.5214,
                "SGD" => 1.4621,
                "SHP" => 0.8573,
                "SLE" => 25.1789,
                "SLL" => 25178.1569,
                "SOS" => 615.6283,
                "SRD" => 37.6373,
                "SSP" => 1697.6666,
                "STN" => 24.5,
                "SYP" => 14012.7672,
                "SZL" => 20.273,
                "THB" => 39.8095,
                "TJS" => 11.8873,
                "TMT" => 3.8002,
            ],
        ];
        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn(json_encode($apiResponse));

        $exchangeRatesService = new ExchangeRatesService($this->client);
        $result = $exchangeRatesService->getExchangeRates($currencyCode);

        $this->assertIsArray($result->getRates());
        $this->assertEquals($apiResponse['conversion_rates'], $result->getRates());
    }

    public function testConstructorThrowsExceptionOnMissingApiKey()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Exchange rates API key not set');

        $apiKeyBackup = $_ENV['EXCHANGE_RATES_API_KEY'];

        try {
            $_ENV['EXCHANGE_RATES_API_KEY'] = '';
            new ExchangeRatesService($this->client);
        } finally {
            $_ENV['EXCHANGE_RATES_API_KEY'] = $apiKeyBackup;
        }
    }

    public function testGetExchangeRatesJsonException()
    {
        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn('invalid json');

        $exchangeRatesService = new ExchangeRatesService($this->client);

        $this->expectException(\JsonException::class);

        $exchangeRatesService->getExchangeRates('USD');
    }
    public function testGetExchangeRatesApiErrorResponse()
    {
        $currencyCode = 'PLZ';
        $apiErrorResponse = [
            'result' => 'error',
            'documentation' => 'https://www.exchangerate-api.com/docs',
            'terms-of-use'=> 'https://www.exchangerate-api.com/terms',
            'error-type' => 'unsupported-code'

        ];

        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn(json_encode($apiErrorResponse));

        $exchangeRatesService = new ExchangeRatesService($this->client);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Getting exchange rates error');

        $exchangeRatesService->getExchangeRates($currencyCode);
    }

    public function testGetExchangeRatesNetworkException()
    {
        $this->client->method('request')
            ->willThrowException(new TransportException());

        $exchangeRatesService = new ExchangeRatesService($this->client);

        $this->expectException(TransportExceptionInterface::class);

        $exchangeRatesService->getExchangeRates('USD');
    }
}
