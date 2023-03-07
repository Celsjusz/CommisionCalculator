<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\Rates;

use ComissionCalculator\Exceptions\ProviderException;

interface RatesProviderInterface
{
    /**
     * @throws ProviderException
     */
    public function getData(): array;
}
