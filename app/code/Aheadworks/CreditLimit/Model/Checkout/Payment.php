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
namespace Aheadworks\CreditLimit\Model\Checkout;

use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class Payment
 *
 * @package Aheadworks\CreditLimit\Model\Checkout
 */
class Payment
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        CustomerManagementInterface $customerManagement
    ) {
        $this->customerManagement = $customerManagement;
    }

    /**
     * Is balance is enough to pay
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isBalanceEnoughToPay($quote)
    {
        $amount = $this->getAvailableAmount($quote);
        if ($this->customerManagement->isAllowedToExceedCreditLimit($quote->getCustomerId())) {
            return true;
        }

        return $amount ? $amount >= $quote->getGrandTotal() : false;
    }

    /**
     * Check if credit limit is exceeded
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isCreditLimitExceeded($quote)
    {
        $result = false;
        if ($quote) {
            $isAllowed = $this->customerManagement->isAllowedToExceedCreditLimit($quote->getCustomerId());
            $amount = $this->getAvailableAmount($quote);
            $result = $isAllowed && $quote->getGrandTotal() > $amount;
        }

        return $result;
    }

    /**
     * Get credit limit exceeded amount
     *
     * @param Quote|null $quote
     * @return float
     */
    public function getCreditLimitExceededAmount($quote)
    {
        $amount = $this->getAvailableAmount($quote);
        return $amount != null ? $quote->getGrandTotal() - $amount : 0;
    }

    /**
     * Get available amount for quote
     *
     * @param Quote|null $quote
     * @return float|null
     */
    public function getAvailableAmount($quote)
    {
        $amount = null;
        if ($quote && $quote->getCustomerId()) {
            $amount = $this->customerManagement->getCreditAvailableAmount(
                $quote->getCustomerId(),
                $quote->getQuoteCurrencyCode()
            );
        }

        return $amount;
    }
}
