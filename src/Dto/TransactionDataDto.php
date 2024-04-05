<?php

namespace App\Dto;

use App\Exception\InvalidTransactionAmountException;

class TransactionDataDto
{
    private int $bin;
    private float $amount;
    private string $currency;

    public static function build(): self
    {
        return new self();
    }

    public function setBin(int $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    public function getBin(): int
    {
        return $this->bin;
    }

    public function setAmount(float $amount): self
    {
        if ($amount < 0) {
            throw new InvalidTransactionAmountException('Transaction amount can\'t be negative');
        }
        $this->amount = $amount;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}