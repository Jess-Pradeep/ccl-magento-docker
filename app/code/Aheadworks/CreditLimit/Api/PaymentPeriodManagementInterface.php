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

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface PaymentPeriodManagementInterface
 * @api
 */
interface PaymentPeriodManagementInterface
{
    /**
     * Update payment period
     *
     * @param int|null $countDays
     * @param int $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updatePeriod(?int $countDays, int $customerId): void;

    /**
     * Update due date
     *
     * @param string $dueDate
     * @param int $customerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDueDate(string $dueDate, int $customerId): void;

    /**
     * Reset due date
     *
     * @param int $customerId
     * @param bool $withLastDueDate
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resetDueDate(int $customerId, bool $withLastDueDate = false): void;

    /**
     * Restore due date
     *
     * @param int $customerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function restoreDueDate(int $customerId): void;

    /**
     * Is the same payment period for customer
     *
     * @param int|null $countDays
     * @param int $customerId
     * @return bool
     */
    public function isSamePaymentPeriod(?int $countDays, int $customerId): bool;

    /**
     * Get payment period
     *
     * @param int $customerId
     * @return int|null
     */
    public function getPaymentPeriod(int $customerId): ?int;

    /**
     * Get due date
     *
     * @param int $customerId
     * @param bool $withTime
     * @return string|null
     */
    public function getDueDate(int $customerId, bool $withTime = false): ?string;

    /**
     * Get calculated due date
     *
     * @param int $customerId
     * @param string $orderCreatedDate
     * @return string|null
     */
    public function getCalcDueDate(int $customerId, string $orderCreatedDate): ?string;

    /**
     * Is payment period expired
     *
     * @param int $customerId
     * @return bool
     */
    public function isPaymentPeriodExpired(int $customerId): bool;

    /**
     * Is place order available
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isPlaceOrderAvailable(int $customerId): bool;
}
