<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Providers\Transactions;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\Transactions\FileTransactionsProvider;

final class FileTransactionsProviderTest extends TestCase
{
    public function testException(): void
    {
        $fileMock = $this->createPartialMock(
            FileTransactionsProvider::class,
            ['getFileData']
        );

        $fileMock->expects($this->once())
            ->method('getFileData')
            ->willThrowException(new ProviderException());

        $this->expectException(ProviderException::class);

        $fileMock->getData();
    }

    public function testFileGetDataWitData(): void
    {
        $testData = ['test', '123'];

        $fileMock = $this->createPartialMock(
            FileTransactionsProvider::class,
            ['getFileData']
        );

        $fileMock->expects($this->once())
            ->method('getFileData')
            ->willReturn($testData);

        $this->assertSame($testData, $fileMock->getData());
    }
}
