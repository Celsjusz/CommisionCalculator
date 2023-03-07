<?php

declare(strict_types = 1);

namespace ComissionCalculator;

use ComissionCalculator\App\Context;
use ComissionCalculator\Config\Config;
use ComissionCalculator\Connectors\CurlConnector;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\BINs\BinListNetProvider;
use ComissionCalculator\Providers\Rates\ExchangeRatesApiRatesProvider;
use ComissionCalculator\Providers\Transactions\FileTransactionsProvider;
use ComissionCalculator\Validators\CountryValidator;
use ComissionCalculator\Views\SimpleView;

require_once ('../vendor/autoload.php');

$fileSource = $argv[1] ?? null;

if ($fileSource) {
    $context = new Context(
        new BinListNetProvider(new CurlConnector()),
        new ExchangeRatesApiRatesProvider(new CurlConnector(), new Config()),
        new FileTransactionsProvider($fileSource),
        new CountryValidator(),
        new Config(),
    );

    try {
        $result = $context->process();
    } catch (ProviderException $e) {
        // log error
        echo $e->getMessage();
        exit;
    }

    echo SimpleView::print($result);
}
