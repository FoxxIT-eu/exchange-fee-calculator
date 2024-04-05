<?php

namespace App\Util;

abstract class CountryHelper
{
    public const EU_COUNTRIES = [
        "Austria",
        "Belgium",
        "Bulgaria",
        "Croatia",
        "Cyprus",
        "Czech Republic",
        "Denmark",
        "Estonia",
        "Finland",
        "France",
        "Germany",
        "Greece",
        "Hungary",
        "Ireland",
        "Italy",
        "Latvia",
        "Lithuania",
        "Luxembourg",
        "Malta",
        "Netherlands",
        "Poland",
        "Portugal",
        "Romania",
        "Slovakia",
        "Slovenia",
        "Spain",
        "Sweden"
    ];

    public static function isCountryInEu(string $countryName): bool
    {
        return (in_array($countryName, static::EU_COUNTRIES));
    }

    public static function getCountryMultiplier(string $countryName): float
    {
        return static::isCountryInEu($countryName) ? 0.01 : 0.02;
    }
}