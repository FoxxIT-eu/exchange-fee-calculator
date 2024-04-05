<?php

namespace App\Util;

use App\Contracts\TransactionFileReaderInterface;
use App\Dto\TransactionDataDto;

class TransactionsTxtFileReader implements TransactionFileReaderInterface
{
    private array $rows = [];
    private int $lastRowIndex = 0;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("$filePath does not exists!");
        }
        $content = file_get_contents($filePath);
        $this->rows = $this->parseData($content);
    }
    private function parseData(string $data): array
    {
        $parsedData = [];
        $data = explode("\n", $data);
        foreach ($data as $item) {
            $item = json_decode($item, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }
            $bin = $item['bin'] ?? '';
            $amount = $item['amount'] ?? '';
            $currency = $item['currency'] ?? '';
            if (empty($bin) || !is_numeric($amount) || empty($currency)) {
                continue;
            }
            $row = TransactionDataDto::build()
                ->setBin($bin)
                ->setAmount($amount)
                ->setCurrency($currency)
            ;
            $parsedData[] = $row;
        }
        return $parsedData;
    }
    public function getNextRow(): TransactionDataDto
    {
        $row = $this->rows[$this->lastRowIndex];
        $this->lastRowIndex++;
        return $row;
    }

    public function haveRows(): bool
    {
        return (count($this->rows) > 0 && count($this->rows) > $this->lastRowIndex);
    }
}