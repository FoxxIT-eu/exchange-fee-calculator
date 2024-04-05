<?php

namespace App\Contracts;

use App\Dto\TransactionDataDto;

interface TransactionFileReaderInterface
{
    public function __construct(string $filePath);
    public function getNextRow(): TransactionDataDto;
    public function haveRows(): bool;
}