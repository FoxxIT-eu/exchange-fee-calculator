<?php

namespace App\Services;

use App\Contracts\ExchangeRatesApiInterface;
use App\Dto\ExchangeRatesDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesService implements ExchangeRatesApiInterface
{
    private const API_URL = "https://v6.exchangerate-api.com/v6/";
    private string $apiKey;

    public function __construct(private HttpClientInterface $client)
    {
        $this->apiKey = $_ENV['EXCHANGE_RATES_API_KEY'] ?? '';
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Exchange rates API key not set');
        }
    }

    public function getExchangeRates(string $currencyCode): ExchangeRatesDto
    {
        $response = $this->client->request(
            'GET',
            static::API_URL . implode('/', [
                $this->apiKey,
                'latest',
                $currencyCode
            ]),
        );

        $data = json_decode($response->getContent(), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \JsonException;
        }
        $result = $data['result'] ?? 'error';
        if ($result != 'success') {
            throw new \RuntimeException('Getting exchange rates error');
        }
        return ExchangeRatesDto::create()
            ->setRates($data['conversion_rates'] ?? [])
            ;
    }
}