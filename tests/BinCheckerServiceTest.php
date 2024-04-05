<?php

namespace App\Tests;

use App\Services\BinCheckerService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BinCheckerServiceTest extends TestCase
{
    private HttpClientInterface $client;
    private ResponseInterface $response;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testGetCardDataSuccess()
    {
        $cardNumber = '123456789';
        $apiResponse = [
            'bin' => '123456',
            'country' => 'Test Country'
        ];

        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn(json_encode($apiResponse));

        $binCheckerService = new BinCheckerService($this->client);
        $result = $binCheckerService->getCardData($cardNumber);

        $this->assertEquals($apiResponse['bin'], $result->getBin());
        $this->assertEquals($apiResponse['country'], $result->getCountry());
    }

    public function testConstructorThrowsExceptionOnMissingApiKey()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('BIN Checker API Key not set');

        $binCheckerApi = $_ENV['BIN_CHECKER_API_KEY'];

        try {
            $_ENV['BIN_CHECKER_API_KEY'] = '';
            new BinCheckerService($this->client);
        } finally {
            $_ENV['BIN_CHECKER_API_KEY'] = $binCheckerApi;
        }
    }

    public function testGetCardDataHandlesClientException()
    {
        $this->client->method('request')
            ->willThrowException(new \Exception('API Request Failed'));

        $binCheckerService = new BinCheckerService($this->client);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Request Failed');

        $binCheckerService->getCardData('123456789');
    }

    public function testGetCardDataWithInvalidApiResponse()
    {
        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn('Invalid JSON');

        $binCheckerService = new BinCheckerService($this->client);

        $this->expectException(\JsonException::class);

        $binCheckerService->getCardData('123456789');
    }

    public function testGetCardDataWithEmptyApiResponse()
    {
        $this->client->method('request')
            ->willReturn($this->response);

        $this->response->method('getContent')
            ->willReturn(json_encode([]));

        $binCheckerService = new BinCheckerService($this->client);

        $result = $binCheckerService->getCardData('123456789');

        $this->assertEmpty($result->getBin());
        $this->assertEmpty($result->getCountry());
    }

    public function testGetCardDataWithPartialApiResponse()
    {
        $this->client->method('request')
            ->willReturn($this->response);

        $partialResponse = ['bin' => '123456'];

        $this->response->method('getContent')
            ->willReturn(json_encode($partialResponse));

        $binCheckerService = new BinCheckerService($this->client);

        $result = $binCheckerService->getCardData('123456789');

        $this->assertEquals('123456', $result->getBin());
        $this->assertEmpty($result->getCountry());
    }

    public function testGetCardDataApiErrorResponse()
    {
        $this->client->method('request')
            ->willReturn($this->response);

        $apiErrorResponse = ['error' => 'Invalid BIN number'];

        $this->response->method('getContent')
            ->willReturn(json_encode($apiErrorResponse));

        $binCheckerService = new BinCheckerService($this->client);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid BIN number');

        $binCheckerService->getCardData('invalid-bin');
    }
}