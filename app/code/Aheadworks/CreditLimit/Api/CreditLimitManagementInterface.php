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
namespace Aheadworks\CreditLimit\Api;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;

/**
 * Interface CreditLimitManagementInterface
 * @api
 */
interface CreditLimitManagementInterface
{
    /**
     * Update credit limit amount for specified customer.
     *
     * Custom credit limit will be configured for specified customer
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    );

    /**
     * Get transaction params by credit limit data for specified customer.
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @param SummaryInterface|null $summary
     * @param int|null $customerGroupId
     * @return TransactionParametersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTransactionParams(
        int $customerId,
        float $creditLimit,
        string $commentToAdmin = '',
        string $commentToCustomer = '',
        $summary = null,
        $customerGroupId = null
    ): TransactionParametersInterface;

    /**
     * Update default credit limit amount for specified customer.
     *
     * This method is used to create transaction for customer with
     * credit limit specified for customer group. Custom credit limit will be reset.
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDefaultCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    );

    /**
     * Update credit limit amount for specified customer
     *
     * @param int $customerId
     * @param float $amount
     * @param string|null $currency
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @param string $poNumber
     * @return boolean
     */
    public function updateCreditBalance(
        $customerId,
        $amount,
        $currency = null,
        $commentToAdmin = '',
        $commentToCustomer = '',
        $poNumber = ''
    );

    /**
     * Spend customer credit balance on order
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return boolean
     */
    public function spendCreditBalanceOnOrder($customerId, $order);

    /**
     * Increase customer credit balance by buying balance unit product
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return boolean
     */
    public function increaseCreditBalanceByUnitPurchase($customerId, $invoice);

    /**
     * Decrease customer credit balance by refunding balance unit product
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return boolean
     */
    public function decreaseCreditBalanceByUnitRefund($customerId, $creditmemo);

    /**
     * Reimburse customer credit balance on cancelled order
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return boolean
     */
    public function reimburseCreditBalanceOnCanceledOrder($customerId, $order);

    /**
     * Refund credit balance on creditmemo
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return boolean
     */
    public function refundCreditBalanceOnCreditmemo($customerId, $order, $creditmemo);
}
