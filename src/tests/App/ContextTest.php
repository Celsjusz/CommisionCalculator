<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Providers\Transactions;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\App\Context;
use ComissionCalculator\App\ContextInterface;
use ComissionCalculator\Config\ConfigInterface;
use ComissionCalculator\Connectors\CurlConnectorInterface;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\BINs\BINsProviderInterface;
use ComissionCalculator\Providers\Rates\RatesProviderInterface;
use ComissionCalculator\Providers\Transactions\TransactionsProviderInterface;
use ComissionCalculator\Validators\CountryValidatorInterface;

final class ContextTest extends TestCase
{
    protected TransactionsProviderInterface $transactionsProvider;
    protected BINsProviderInterface $BINsProvider;
    protected RatesProviderInterface $ratesProvider;
    protected CurlConnectorInterface $curlConnector;
    protected CountryValidatorInterface $countryValidator;
    protected ConfigInterface $config;
    protected ContextInterface $class;

    public function setUp(): void
    {
        $this->transactionsProvider = $this->createMock(TransactionsProviderInterface::class);
        $this->BINsProvider = $this->createMock(BINsProviderInterface::class);
        $this->ratesProvider = $this->createMock(RatesProviderInterface::class);
        $this->curlConnector = $this->createMock(CurlConnectorInterface::class);
        $this->countryValidator = $this->createMock(CountryValidatorInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);

        $this->class = new Context(
            $this->BINsProvider,
            $this->ratesProvider,
            $this->transactionsProvider,
            $this->countryValidator,
            $this->config
        );
    }

    public function testEmptyTransactions(): void
    {
        $this->transactionsProvider->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $this->assertSame(null, $this->class->process());
    }

    public function testEmptyBin(): void
    {
        $bin = '555';

        $this->transactionsProvider->expects($this->once())
            ->method('getData')
            ->willReturn(
                [
                    ['bin' => $bin],
                ],
           );

        $this->BINsProvider->expects($this->once())
            ->method('getData')
            ->with($bin)
            ->willThrowException(new ProviderException());

        $this->expectException(ProviderException::class);

        $this->class->process();
    }

    public function testEU(): void
    {
        $amount = 10;
        $bin = '555';
        $country = 'DE';
        $currency = 'EUR';

        $this->transactionsProvider->expects($this->exactly(2))
            ->method('getData')
            ->willReturn(
                [
                    [
                        'bin' => $bin,
                        'currency' => $currency,
                        'amount' => $amount,
                    ],
                ]
            );

        $this->BINsProvider->expects($this->exactly(2))
            ->method('getData')
            ->with($bin)
            ->willReturn(
                [
                    'country' => [
                        'alpha2' => $country
                    ]
                ]
            );

        ////
        $this->countryValidator->expects($this->exactly(2))
            ->method('isCountryEu')
            ->with($country)
            ->willReturnOnConsecutiveCalls(true, false);

        $this->assertSame([$amount * ContextInterface::EU_COMMISSION_RATE], $this->class->process());
        $this->assertSame([$amount * ContextInterface::NON_EU_COMMISSION_RATE], $this->class->process());
    }

    public function testDefaultCurrency(): void
    {
        $amount = 10;
        $bin = '555';
        $country = 'DE';
        $currency = 'EUR';

        $this->transactionsProvider->expects($this->exactly(4))
            ->method('getData')
            ->willReturn(
                [
                    [
                        'bin' => $bin,
                        'currency' => $currency,
                        'amount' => $amount,
                    ],
                ]
            );

        $this->BINsProvider->expects($this->exactly(4))
            ->method('getData')
            ->with($bin)
            ->willReturn(
                [
                    'country' => [
                        'alpha2' => $country
                    ]
                ]
            );

        $this->config->expects($this->exactly(4))
            ->method('isRoundUpEnabled')
            ->willReturnOnConsecutiveCalls(true, false, true, false);

        $rate = 1; // EUR to EUR is 1

        $this->countryValidator->expects($this->exactly(4))
            ->method('isCountryEu')
            ->with($country)
            ->willReturnOnConsecutiveCalls(true, true, false, false);

        $this->assertSame(
            [ceil( ($amount / $rate) * ContextInterface::EU_COMMISSION_RATE * 100) / 100],
            $this->class->process()
        );

        $this->assertSame(
            [($amount / $rate) * ContextInterface::EU_COMMISSION_RATE],
            $this->class->process()
        );

        $this->assertSame(
            [ceil( ($amount / $rate) * ContextInterface::NON_EU_COMMISSION_RATE * 100) / 100],
            $this->class->process()
        );

        $this->assertSame(
            [($amount / $rate) * ContextInterface::NON_EU_COMMISSION_RATE],
            $this->class->process()
        );
    }

    public function testNonDefaultCurrency(): void
    {
        $amount = 10;
        $bin = '555';
        $country = 'DE';
        $currency = 'GBP';
        $rates = [
            4.265456,
            562.4516,
            0.5465,
            1.25,
        ];

        $this->transactionsProvider->expects($this->exactly(4))
            ->method('getData')
            ->willReturn(
                [
                    [
                        'bin' => $bin,
                        'currency' => $currency,
                        'amount' => $amount,
                    ],
                ]
            );

        $this->BINsProvider->expects($this->exactly(4))
            ->method('getData')
            ->with($bin)
            ->willReturn(
                [
                    'country' => [
                        'alpha2' => $country
                    ]
                ]
            );

        $this->config->expects($this->exactly(4))
            ->method('isRoundUpEnabled')
            ->willReturnOnConsecutiveCalls(true, false, true, false);

        $this->ratesProvider->expects($this->exactly(4))
            ->method('getData')
            ->willReturnOnConsecutiveCalls(
                [$currency => $rates[0]],
                [$currency => $rates[1]],
                [$currency => $rates[2]],
                [$currency => $rates[3]]);

        $this->countryValidator->expects($this->exactly(4))
            ->method('isCountryEu')
            ->with($country)
            ->willReturnOnConsecutiveCalls(true, true, false, false);


        $calculatedRates = [];
        foreach ($rates as $rate) {
            $calculatedRates[] = ($amount / $rate);
        }

        $this->assertSame(
            [ceil(($amount / $calculatedRates[0]) * ContextInterface::EU_COMMISSION_RATE * 100) / 100],
            $this->class->process()
        );

        $this->assertSame(
            [($amount / $calculatedRates[1]) * ContextInterface::EU_COMMISSION_RATE],
            $this->class->process()
        );

        $this->assertSame(
            [ceil(($amount / $calculatedRates[2]) * ContextInterface::NON_EU_COMMISSION_RATE * 100) / 100],
            $this->class->process()
        );

        $this->assertSame(
            [($amount / $calculatedRates[3]) * ContextInterface::NON_EU_COMMISSION_RATE],
            $this->class->process()
        );
    }
}
