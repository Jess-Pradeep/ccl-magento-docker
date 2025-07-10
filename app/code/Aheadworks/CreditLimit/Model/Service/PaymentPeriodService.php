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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\CreditLimit\Model\CreditSummary\Checker as CreditSummaryChecker;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class PaymentPeriodService
 */
class PaymentPeriodService implements PaymentPeriodManagementInterface
{
    /**
     * PaymentPeriodService constructor.
     *
     * @param SummaryRepositoryInterface $summaryRepository
     * @param TimezoneInterface $timezone
     * @param CreditSummaryChecker $creditSummaryChecker
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        private SummaryRepositoryInterface $summaryRepository,
        private TimezoneInterface $timezone,
        private CreditSummaryChecker $creditSummaryChecker,
        private CustomerManagementInterface $customerManagement
    ) {
    }

    /**
     * Update payment period
     *
     * @param int|null $countDays
     * @param int $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updatePeriod(?int $countDays, int $customerId): void
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary) {
            $summary->setPaymentPeriod($countDays);
            $summary->setDueDate(null);
            $this->summaryRepository->save($summary);
        }
    }

    /**
     * Update due date
     *
     * @param string $dueDate
     * @param int $customerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDueDate(string $dueDate, int $customerId): void
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary) {
            $summary->setDueDate($dueDate);
            $summary->setLastDueDate($dueDate);
            $this->summaryRepository->save($summary);
        }
    }

    /**
     * Reset due date
     *
     * @param int $customerId
     * @param bool $withLastDueDate
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resetDueDate(int $customerId, bool $withLastDueDate = false): void
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary) {
            $summary->setDueDate(null);
            if ($withLastDueDate) {
                $summary->setLastDueDate(null);
            }
            $this->summaryRepository->save($summary);
        }
    }

    /**
     * Restore due date
     *
     * @param int $customerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function restoreDueDate(int $customerId): void
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary && $summary->getPaymentPeriod()) {
            $summary->setDueDate(
                $summary->getLastDueDate()
            );
            $this->summaryRepository->save($summary);
        }
    }

    /**
     * Is the same payment period for customer
     *
     * @param int|null $countDays
     * @param int $customerId
     * @return bool
     */
    public function isSamePaymentPeriod(?int $countDays, int $customerId): bool
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        return $summary && $summary->getPaymentPeriod() === $countDays;
    }

    /**
     * Get payment period
     *
     * @param int $customerId
     * @return int|null
     */
    public function getPaymentPeriod(int $customerId): ?int
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        return $summary ? $summary->getPaymentPeriod() : null;
    }

    /**
     * Get due date
     *
     * @param int $customerId
     * @param bool $withTime
     * @return string|null
     */
    public function getDueDate(int $customerId, bool $withTime = false): ?string
    {
        $dueDate = null;
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary && $summary->getDueDate()) {
            $dueDate = $summary->getDueDate();
            if (!$withTime) {
                $dueDate = $this->timezone
                    ->date(strtotime($summary->getDueDate()))
                    ->format(StdlibDateTime::DATE_PHP_FORMAT);
            }
        }
        return $dueDate;
    }

    /**
     * Get calculated due date
     *
     * @param int $customerId
     * @param string $orderCreatedDate
     * @return string|null
     */
    public function getCalcDueDate(int $customerId, string $orderCreatedDate): ?string
    {
        $dueDate = null;
        $countDays = $this->getPaymentPeriod($customerId);
        if ($countDays) {
            $dueDate = $this->timezone
                ->date(strtotime($orderCreatedDate . "+$countDays days"))
                ->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }
        return $dueDate;
    }

    /**
     * Is payment period expired
     *
     * @param int $customerId
     * @return bool
     */
    public function isPaymentPeriodExpired(int $customerId): bool
    {
        $result = false;
        $dueDate = $this->getDueDate($customerId, true);
        if ($dueDate) {
            $dueDate = new \DateTime($dueDate);
            $dueDate = $this->timezone->date($dueDate);
            $currentDate = $this->timezone->date();
            $diff = $dueDate->getTimestamp() - $currentDate->getTimestamp();
            return $diff < 0;
        }
        return $result;
    }

    /**
     * Is place order available
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isPlaceOrderAvailable(int $customerId): bool
    {
        $summary = $this->customerManagement->getCreditLimitSummary($customerId);
        if ($summary) {
            $isBalancePositive = $this->creditSummaryChecker->isCreditBalancePositive($summary);
            $isPaymentPeriodExpired = $this->isPaymentPeriodExpired($customerId);
            return !($isPaymentPeriodExpired && !$isBalancePositive);
        }

        return true;
    }
}
