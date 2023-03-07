<?php

declare(strict_types=1);

namespace ComissionCalculator\App;

use ComissionCalculator\Config\ConfigInterface;
use ComissionCalculator\Exceptions\ProviderException;
use ComissionCalculator\Providers\BINs\BINsProviderInterface;
use ComissionCalculator\Providers\Rates\RatesProviderInterface;
use ComissionCalculator\Providers\Transactions\TransactionsProviderInterface;
use ComissionCalculator\Validators\CountryValidatorInterface;

class Context implements ContextInterface
{
    public function __construct(
        protected readonly BINsProviderInterface $BINsProvider,
        protected readonly RatesProviderInterface $ratesProvider,
        protected readonly TransactionsProviderInterface $transactionsProvider,
        protected readonly CountryValidatorInterface $countryValidator,
        protected readonly ConfigInterface $config,
    ) {}

    /**
     * @return float[]|null
     * @throws ProviderException
     */
    public function process(): ?array
    {
        $result = [];
        if ($transactions = $this->getTransactions()) {
            foreach ($transactions as $transaction) {
                $BINInfo = $this->getBINInfo((string) $transaction['bin']);
                $rate = $this->getRate($transaction);

                $commissionRate = $this->getCommissionRate(
                    $this->countryValidator->isCountryEu($BINInfo['country']['alpha2'])
                );

                $commission = $this->calculateCommission(
                    (float) $transaction['amount'],
                    $rate,
                    $commissionRate
                );

               $result[] = $commission;
            }

            return $result;
        }

        return null;
    }

    /**
     * @throws ProviderException
     */
    protected function getTransactions(): array
    {
        return $this->transactionsProvider->getData();
    }

    /**
     * @throws ProviderException
     */
    protected function getBINInfo(string $bin): array
    {
        return $this->BINsProvider->getData($bin);
    }

    /**
     * @throws ProviderException
     */
    protected function getRate(array $transaction): float
    {
        $rate = 1.0;
        if ($transaction['currency'] !== $this::DEFAULT_CURRENCY) {
            $rates = $this->ratesProvider->getData();
            $rate = $transaction['amount'] / $rates[$transaction['currency']];
        }

        return $rate;
    }

    protected function getCommissionRate(bool $isEu): float
    {
        return $isEu ? $this::EU_COMMISSION_RATE : $this::NON_EU_COMMISSION_RATE;
    }

    protected function calculateCommission(
        float $amount,
        float $rate,
        float $commissionRate
    ): float {
        $commission = ($amount / $rate) * $commissionRate;

        return (float) ($this->config->isRoundUpEnabled() ? ceil($commission * 100) / 100 : $commission);
    }
}
