<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\BINs;

use ComissionCalculator\Connectors\CurlConnectorInterface;
use ComissionCalculator\Providers\AbstractExternalProvider;

class BinListNetProvider extends AbstractExternalProvider implements BINsProviderInterface
{
    /** @var string */
    protected const URL = 'https://lookup.binlist.net/{BIN}';

    public function __construct(
        private readonly CurlConnectorInterface $curlConnector,
    ) {}

    public function getData(string $bin): array
    {
        $url = str_replace('{BIN}', $bin, $this::URL);

        $response = $this->curlConnector->getData($url);
        if ($response
            && $response[0] === '{'
            && $jsonDecoded = \json_decode($response, true)
        ) {
            return $jsonDecoded;
        } else {
            // log error
        }

        $this->throwEmptyResponseException();
        return [];
    }
}
