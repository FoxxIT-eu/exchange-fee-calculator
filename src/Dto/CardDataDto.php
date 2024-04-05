<?php

namespace App\Dto;

class CardDataDto
{
    private string $country;
    private string $bin;
    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setBin(string $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    public function getBin(): string
    {
        return $this->bin;
    }
    public static function create(): self
    {
        return new self();
    }
}