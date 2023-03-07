<?php

declare(strict_types=1);

namespace ComissionCalculator\Config;

use ComissionCalculator\Providers\Rates\ExchangeRatesApiRatesProvider;

class Config implements ConfigInterface
{
    protected const API_KEYS = [
        ExchangeRatesApiRatesProvider::class => 'apikey' // for simplicity, obviously we don't want that in repo :)
    ];

    public function isRoundUpEnabled(): bool
    {
        return true;
    }

    public function getApiKey(string $service): ?string
    {
        return $this::API_KEYS[$service] ?? null;
    }
}
