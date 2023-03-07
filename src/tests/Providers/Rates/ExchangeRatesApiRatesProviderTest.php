<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Providers\Rates;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\Config\ConfigInterface;
use ComissionCalculator\Connectors\CurlConnectorInterface;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\Rates\ExchangeRatesApiRatesProvider;
use ComissionCalculator\Providers\Rates\RatesProviderInterface;

final class ExchangeRatesApiRatesProviderTest extends TestCase
{
    protected const RESPONSE_MOCK =
        <<<MOCK
{
    "success": true,
    "timestamp": 1462564331,
    "base": "EUR",
    "date": "2023-03-02",
    "rates": {
        "AED": 3.894148,
        "CLP": 866.686772, 
        "GMD": 64.726755,
        "GTQ": 8.28189,
        "HNL": 26.148504,
        "HRK": 7.479719,
        "KGS": 92.684456,
        "KHR": 4310.468838,
        "MGA": 4552.783927,
        "MRO": 378.502649
    }
}
MOCK;

    private CurlConnectorInterface $curlConnector;
    private RatesProviderInterface $class;

    public function setUp(): void
    {
        $this->curlConnector = $this->createMock(CurlConnectorInterface::class);
        $this->class = new ExchangeRatesApiRatesProvider(
            $this->curlConnector,
            $this->createMock(ConfigInterface::class)
        );
    }

    public function testGetDataEmpty(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->willReturn('');

        $this->expectException(ProviderException::class);
        $this->class->getData('');
    }

    public function testGetDataFalse(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->willReturn(false);

        $this->expectException(ProviderException::class);
        $this->class->getData('');
    }

    public function testGetDataWithBrokenJson(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->withAnyParameters()
            ->willReturn('{"rates": {a}}');

        $this->expectException(ProviderException::class);

        $this->class->getData();
    }

    public function testGetDataWithValidJson(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->withAnyParameters()
            ->willReturn('{"rates": {"TEST":123}}');

        $this->assertSame(['TEST' => 123], $this->class->getData());
    }

    public function testGetDataWithMock(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->willReturn($this::RESPONSE_MOCK);

        $this->assertSame(
            json_decode($this::RESPONSE_MOCK, true)['rates'],
            $this->class->getData()
        );
    }
}
