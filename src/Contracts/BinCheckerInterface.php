<?php

namespace App\Contracts;

use App\Dto\CardDataDto;

interface BinCheckerInterface
{
    public function getCardData(string $cardNumber): CardDataDto;
}