<?php

declare(strict_types=1);

namespace ComissionCalculator\Validators;

interface CountryValidatorInterface
{
    public function isCountryEu(string $countryCode): bool;
}
