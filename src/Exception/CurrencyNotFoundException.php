<?php

namespace App\Exception;

class CurrencyNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        $this->message = "Currency Code Not Found";
        parent::__construct();
    }
}