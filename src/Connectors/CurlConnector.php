<?php

declare(strict_types=1);

namespace ComissionCalculator\Connectors;

class CurlConnector implements CurlConnectorInterface
{
    public function getData(string $url, array $headers = ['Accept: application/json']): bool|string
    {
        $curlHandler = curl_init();

        // no timeout, lets stick with default 300s
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curlHandler);

        curl_close($curlHandler);
        if (curl_errno($curlHandler)) {
            //log error
            return '';
        }

        return $response;
    }
}
