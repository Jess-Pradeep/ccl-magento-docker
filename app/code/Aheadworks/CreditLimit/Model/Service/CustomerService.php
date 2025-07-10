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
namespace Aheadworks\CreditLimit\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Currency\RateConverter;
use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\Provider as BalanceUnitProvider;

/**
 * Class CustomerService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * CustomerService constructor.
     *
     * @param SummaryRepositoryInterface $summaryRepository
     * @param RateConverter $rateConverter
     * @param PriceCurrencyInterface $priceCurrency
     * @param Config $config
     * @param BalanceUnitProvider $balanceUnitProvider
     */
    public function __construct(
        private SummaryRepositoryInterface $summaryRepository,
        private RateConverter $rateConverter,
        private PriceCurrencyInterface $priceCurrency,
        private Config $config,
        private BalanceUnitProvider $balanceUnitProvider
    ) {
    }

    /**
     * @inheritdoc
     */
    public function isCreditLimitAvailable($customerId)
    {
        return $this->getCreditLimitSummary((int)$customerId) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function isCreditLimitCustom($customerId)
    {
        $summary = $this->getCreditLimitSummary((int)$customerId);
        if ($summary) {
            return $summary->getIsCustomCreditLimit();
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function isAllowedToExceedCreditLimit($customerId)
    {
        $summary = $this->getCreditLimitSummary((int)$customerId);
        return $summary ? $summary->getIsAllowedToExceed() : false;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isAllowedToUpdateCreditBalance($customerId, $websiteId)
    {
        try {
            $this->balanceUnitProvider->getProduct();
        } catch (LocalizedException $exception) {
            return false;
        }

        return $this->config->isAllowedToUpdateCreditBalance($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getCreditLimitAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary((int)$customerId);
        if (!$summary || $summary->getCreditLimit() === null) {
            return null;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditLimit(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditLimit());
    }

    /**
     * @inheritdoc
     */
    public function getCreditBalanceAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary((int)$customerId);
        if (!$summary) {
            return 0;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditBalance(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditBalance());
    }

    /**
     * @inheritdoc
     */
    public function getCreditAvailableAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary((int)$customerId);
        if (!$summary) {
            return 0;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditAvailable(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditAvailable());
    }

    /**
     * Get credit limit summary
     *
     * @param int $customerId
     * @return SummaryInterface|null
     */
    public function getCreditLimitSummary(int $customerId): ?SummaryInterface
    {
        try {
            return $this->summaryRepository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $noSuchEntityException) {
            return null;
        }
    }
}
