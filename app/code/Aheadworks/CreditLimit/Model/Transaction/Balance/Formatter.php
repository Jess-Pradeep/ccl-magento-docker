<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Transaction\Balance;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Directory\Model\CurrencyFactory;
use Aheadworks\CreditLimit\Model\Currency\Manager as CurrencyManager;

/**
 * Class Formatter
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Balance
 */
class Formatter
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var CurrencyManager
     */
    private $currencyManager;

    /**
     * Formatter constructor.
     *
     * @param PriceCurrencyInterface $priceFormatter
     * @param CurrencyFactory $currencyFactory
     * @param CurrencyManager $currencyManager
     */
    public function __construct(
        PriceCurrencyInterface $priceFormatter,
        CurrencyFactory $currencyFactory,
        CurrencyManager $currencyManager
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->currencyFactory = $currencyFactory;
        $this->currencyManager = $currencyManager;
    }

    /**
     * Format transaction amount
     *
     * @param array $transactionData
     * @return string
     */
    public function formatTransactionAmount($transactionData)
    {
        $rateToActionCurrency = isset($transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY] : 1;
        $rateToActionCurrency = $rateToActionCurrency ?: 1;
        $rateToCreditCurrency = isset($transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY] : 0;
        $amount = $transactionData[TransactionInterface::AMOUNT];

        if ($rateToCreditCurrency) {
            $creditAmountConverted = $amount * $rateToCreditCurrency;
            $actionAmountConverted = $amount * $rateToActionCurrency;
        } else {
            $actionAmountConverted = $amount;
            if ($transactionData[TransactionInterface::CREDIT_CURRENCY] ===
                $transactionData[TransactionInterface::ACTION_CURRENCY]) {
                $creditAmountConverted = $amount;
            } else {
                $creditAmountConverted = $amount / $rateToActionCurrency;
            }
        }

        $creditCurrency = $transactionData[TransactionInterface::CREDIT_CURRENCY];
        $currency = $this->currencyFactory->create()->load($creditCurrency);
        if ($creditAmountConverted == $actionAmountConverted) {
            $result = $this->formatPrice($creditAmountConverted, $currency);
        } else {
            $actionFormattedPrice = $this->formatPrice(
                $actionAmountConverted,
                $transactionData[TransactionInterface::ACTION_CURRENCY]
            );
            $result = sprintf(
                '%s (%s)<br>%s/%s: %s',
                $this->formatPrice($creditAmountConverted, $currency),
                $actionFormattedPrice,
                $transactionData[TransactionInterface::CREDIT_CURRENCY],
                $transactionData[TransactionInterface::ACTION_CURRENCY],
                number_format($transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY], 4)
            );
        }

        return $result;
    }

    /**
     * Get formatted amount data
     * Example result - ['value' => "string", 'currency' => "string"]
     *
     * @param array $transactionData
     * @param bool $isShowPlus
     * @return array
     */
    public function getFormattedAmountData(array $transactionData, bool $isShowPlus = false): array
    {
        $rateToActionCurrency = isset($transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY] : 1;
        $rateToActionCurrency = $rateToActionCurrency ?: 1;
        $rateToCreditCurrency = isset($transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY] : 0;
        $amount = $transactionData[TransactionInterface::AMOUNT];

        if ($rateToCreditCurrency) {
            $creditAmountConverted = $amount * $rateToCreditCurrency;
        } else {
            if ($transactionData[TransactionInterface::CREDIT_CURRENCY] ===
                $transactionData[TransactionInterface::ACTION_CURRENCY]) {
                $creditAmountConverted = $amount;
            } else {
                $creditAmountConverted = $amount / $rateToActionCurrency;
            }
        }

        return $this->currencyManager->getFormattedPriceData(
            (float)$creditAmountConverted,
            $transactionData[TransactionInterface::CREDIT_CURRENCY],
            $isShowPlus
        );
    }

    /**
     * Get formatted balance data
     * Example result - ['value' => "string", 'currency' => "string"]
     *
     * @param float $balanceValue
     * @param string $creditCurrency
     * @param bool $isShowPlus
     * @return array
     */
    public function getFormattedBalanceData(
        float $balanceValue,
        string $creditCurrency,
        bool $isShowPlus = false
    ): array {
        return $this->currencyManager->getFormattedPriceData(
            (float)$balanceValue,
            $creditCurrency,
            $isShowPlus
        );
    }

    /**
     * Format price
     *
     * @param float $price
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     * @return string
     */
    private function formatPrice($price, $currency)
    {
        return $this->priceFormatter->format(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currency
        );
    }
}
