<?php

declare(strict_types = 1);

namespace ComissionCalculator\Providers\Transactions;

use ComissionCalculator\Exceptions\ProviderException;

class FileTransactionsProvider implements TransactionsProviderInterface
{
    public function __construct(protected readonly string $fileName) {}

    public function getData(): array
    {
        return $this->getFileData();
    }

    /**
     * @throws ProviderException
     */
    public function getFileData(): array
    {
        if (file_exists($this->fileName)) {
            $handle = fopen($this->fileName, 'r');
            $result = [];
            $lines = 1;
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (!($decoded = json_decode($line, true))) {
                        throw new ProviderException(
                            sprintf(
                                'File is not JSON, error on line %s in %s',
                                $lines,
                                $this->fileName
                            )
                        );
                    }

                    $result[] = $decoded;
                    $lines++;
                }

                fclose($handle);

                return $result;
            }
        }

        throw new ProviderException(
            sprintf(
                'File %s does not exist.',
                $this->fileName
            )
        );
    }
}
