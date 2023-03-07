<?php

declare(strict_types=1);

namespace ComissionCalculator\Config;

interface ConfigInterface
{
    public function isRoundUpEnabled(): bool;
    public function getApiKey(string $service): ?string;
}
