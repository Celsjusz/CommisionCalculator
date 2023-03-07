<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\Transactions;

use ComissionCalculator\Exceptions\ProviderException;

interface TransactionsProviderInterface
{
    /**
     * @throws ProviderException
     */
    public function getData(): array;
}
