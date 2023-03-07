<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\BINs;

use ComissionCalculator\Exceptions\ProviderException;

interface BINsProviderInterface
{
    /**
     * @throws ProviderException
     */
    public function getData(string $bin): array;
}
