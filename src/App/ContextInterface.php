<?php

declare(strict_types=1);

namespace ComissionCalculator\App;

use ComissionCalculator\Exceptions\ProviderException;

interface ContextInterface
{
    /** @var string */
    public const DEFAULT_CURRENCY = 'EUR';

    /** @var float */
    public const NON_EU_COMMISSION_RATE = 0.02;

    /** @var float */
    public const EU_COMMISSION_RATE = 0.01;

    /**
     * @throws ProviderException
     */
    public function process(): ?array;
}
