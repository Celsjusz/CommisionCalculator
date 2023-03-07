<?php

declare(strict_types=1);

namespace ComissionCalculator\Connectors;

interface CurlConnectorInterface
{
    public function getData(string $url, array $headers = ['Accept: application/json']): bool|string;
}
