<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Providers\BINs;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\Connectors\CurlConnectorInterface;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\BINs\BinListNetProvider;
use ComissionCalculator\Providers\BINs\BINsProviderInterface;

final class BinListNetProviderTest extends TestCase
{
    protected const RESPONSE_MOCK = '{"test":"123","all":"good"}';

    private CurlConnectorInterface $curlConnector;
    private BINsProviderInterface $class;

    public function setUp(): void
    {
        $this->curlConnector = $this->createMock(CurlConnectorInterface::class);
        $this->class = new BinListNetProvider($this->curlConnector);
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

    public function testFileGetDataWithMock(): void
    {
        $this->curlConnector->expects($this->once())
            ->method('getData')
            ->willReturn($this::RESPONSE_MOCK);

        $this->assertSame(
            json_decode($this::RESPONSE_MOCK, true),
            $this->class->getData('')
        );
    }
}
