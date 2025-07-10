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
namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SummaryInterface
 * @api
 */
interface SummaryInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const SUMMARY_ID = 'summary_id';
    const CUSTOMER_ID = 'customer_id';
    const WEBSITE_ID = 'website_id';
    const CREDIT_LIMIT = 'credit_limit';
    const IS_CUSTOM_CREDIT_LIMIT = 'is_custom_credit_limit';
    const IS_ALLOWED_TO_EXCEED = 'is_allowed_to_exceed';
    const CREDIT_BALANCE = 'credit_balance';
    const CREDIT_AVAILABLE = 'credit_available';
    const CURRENCY = 'currency';
    const LAST_PAYMENT_DATE = 'last_payment_date';
    const COMPANY_ID = 'company_id';
    const PAYMENT_PERIOD = 'payment_period';
    const DUE_DATE = 'due_date';
    const LAST_DUE_DATE = 'last_due_date';
    const NEGATIVE_BALANCE_DATE = 'negative_balance_date';
    /**#@-*/

    /**
     * Set summary ID
     *
     * @param int $summaryId
     * @return SummaryInterface
     */
    public function setSummaryId($summaryId);

    /**
     * Get summary ID
     *
     * @return int
     */
    public function getSummaryId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return SummaryInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return SummaryInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get website ID
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set credit limit
     *
     * @param float|null $creditLimit
     * @return SummaryInterface
     */
    public function setCreditLimit($creditLimit);

    /**
     * Get credit limit
     *
     * @return float|null
     */
    public function getCreditLimit();

    /**
     * Set is custom credit limit
     *
     * @param bool $isCustomCreditLimit
     * @return SummaryInterface
     */
    public function setIsCustomCreditLimit($isCustomCreditLimit);

    /**
     * Get is custom credit limit
     *
     * @return bool
     */
    public function getIsCustomCreditLimit();

    /**
     * Set is allowed to exceed credit limit
     *
     * @param bool $isAllowed
     * @return SummaryInterface
     */
    public function setIsAllowedToExceed($isAllowed);

    /**
     * Get is allowed to exceed credit limit
     *
     * @return bool
     */
    public function getIsAllowedToExceed();

    /**
     * Set credit balance
     *
     * @param float $creditBalance
     * @return SummaryInterface
     */
    public function setCreditBalance($creditBalance);

    /**
     * Get credit balance
     *
     * @return float
     */
    public function getCreditBalance();

    /**
     * Set credit available
     *
     * @param float $creditAvailable
     * @return float
     */
    public function setCreditAvailable($creditAvailable);

    /**
     * Get credit available
     *
     * @return float
     */
    public function getCreditAvailable();

    /**
     * Set credit limit currency
     *
     * @param string $currency
     * @return SummaryInterface
     */
    public function setCurrency($currency);

    /**
     * Get credit limit currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set last payment date
     *
     * @param string $lastPaymentDate
     * @return SummaryInterface
     */
    public function setLastPaymentDate($lastPaymentDate);

    /**
     * Get last payment date
     *
     * @return string
     */
    public function getLastPaymentDate();

    /**
     * Set company ID
     *
     * @param int $companyId
     * @return SummaryInterface
     */
    public function setCompanyId($companyId);

    /**
     * Get company ID
     *
     * @return string
     */
    public function getCompanyId();

    /**
     * Set payment period
     *
     * @param int|null $countDays
     * @return SummaryInterface
     */
    public function setPaymentPeriod(?int $countDays): SummaryInterface;

    /**
     * Get payment period
     *
     * @return int|null
     */
    public function getPaymentPeriod(): ?int;

    /**
     * Set due date
     *
     * @param string|null $date
     * @return SummaryInterface
     */
    public function setDueDate(?string $date): SummaryInterface;

    /**
     * Get due date
     *
     * @return string|null
     */
    public function getDueDate(): ?string;

    /**
     * Set last due date
     *
     * @param string|null $date
     * @return SummaryInterface
     */
    public function setLastDueDate(?string $date): SummaryInterface;

    /**
     * Get last due date
     *
     * @return string|null
     */
    public function getLastDueDate(): ?string;

    /**
     * Get negative balance date
     *
     * @return null|string
     */
    public function getNegativeBalanceDate(): ?string;

    /**
     * Set negative balance date
     *
     * @param null|string $negativeBalanceDate
     * @return $this
     */
    public function setNegativeBalanceDate(?string $negativeBalanceDate): self;

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
    );
}
