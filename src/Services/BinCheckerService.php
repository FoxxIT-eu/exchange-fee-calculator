<?php

namespace App\Services;

use App\Contracts\BinCheckerInterface;
use App\Dto\CardDataDto;
use App\Exception\InvalidBinNumberException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinCheckerService implements BinCheckerInterface
{
    private const API_URL = 'https://api.apilayer.com/bincheck/';
    private string $apiKey = '';

    public function __construct(private readonly HttpClientInterface $client)
    {
        $this->apiKey = $_ENV['BIN_CHECKER_API_KEY'] ?? '';
        if (empty($this->apiKey)) {
            throw new \RuntimeException('BIN Checker API Key not set');
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCardData(string $cardNumber): CardDataDto
    {
        $response = $this->client->request(
            'GET',
            static::API_URL . $cardNumber,
            [
                'headers' => [
                    'apikey' => $this->apiKey,
                ],
            ]
        );
        $content = json_decode($response->getContent(), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \JsonException;
        }
        if (array_key_exists('error', $content)) {
            throw new InvalidBinNumberException($content['error']);
        }
        return CardDataDto::create()
            ->setBin($content['bin'] ?? '')
            ->setCountry($content['country'] ?? '')
        ;
    }
}