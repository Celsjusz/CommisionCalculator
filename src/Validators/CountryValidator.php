<?php

declare(strict_types=1);

namespace ComissionCalculator\Validators;

class CountryValidator implements CountryValidatorInterface
{
    public function __construct(
        public readonly array $EUCountries =
        [
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PO',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK',
        ]
    ) {}

    public function isCountryEu(string $countryCode): bool
    {
        return in_array($countryCode, $this->EUCountries);
    }
}
