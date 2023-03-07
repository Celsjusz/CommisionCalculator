<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\Rates;

use ComissionCalculator\App\ContextInterface;
use ComissionCalculator\Config\ConfigInterface;
use ComissionCalculator\Connectors\CurlConnectorInterface;
use ComissionCalculator\Providers\AbstractExternalProvider;

class ExchangeRatesApiRatesProvider extends AbstractExternalProvider implements RatesProviderInterface
{
    /** @var string */
    protected const URL = 'https:/api.apilayer.com/exchangerates_data/latest?base=' . ContextInterface::DEFAULT_CURRENCY;

    public function __construct(
        private readonly CurlConnectorInterface $curlConnector,
        private readonly ConfigInterface $config,
    ) {}

    public function getData(): array
    {
        $response = $this->curlConnector->getData(
            $this::URL,
            [
                'Content-Type: text/plain',
                'apikey: ' . $this->config->getApiKey(self::class),
            ]
        );

        if ($response && $response[0] === '{') { // fast check for json
            $jsonDecoded = \json_decode($response, true);

            if (isset($jsonDecoded['rates']) && !empty($jsonDecoded['rates'])) {
                return $jsonDecoded['rates'];
            } else {
                // log error
            }
        }

        $this->throwEmptyResponseException();
        return [];
    }
}
