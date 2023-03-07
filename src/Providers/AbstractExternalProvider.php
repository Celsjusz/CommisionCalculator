<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers;

use ComissionCalculator\Exceptions\ProviderException;

abstract class AbstractExternalProvider
{
    /**
     * @throws ProviderException
     */
    public function throwEmptyResponseException(): void
    {
        throw new ProviderException(
            sprintf('Empty response from %s', $this::class)
        );
    }
}
