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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;
use Aheadworks\Ca\Model\Customer\Checker\CustomerStatus;

/**
 * Class StoreCreditServicePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class StoreCreditServicePlugin
{
    /**
     * @var StoreCreditManagement
     */
    private $storeCreditManagement;

    /**
     * @var CustomerStatus
     */
    private $customerStatus;

    /**
     * @param StoreCreditManagement $storeCreditManagement
     * @param CustomerStatus $customerStatus
     */
    public function __construct(
        StoreCreditManagement $storeCreditManagement,
        CustomerStatus $customerStatus
    ) {
        $this->storeCreditManagement = $storeCreditManagement;
        $this->customerStatus = $customerStatus;
    }

    /**
     * Spend customer store credit on checkout
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $spendStoreCredit
     * @param OrderInterface $order
     * @param int $websiteId
     * @return array
     */
    public function beforeSpendStoreCreditOnCheckout($subject, $customerId, $spendStoreCredit, $order, $websiteId)
    {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $spendStoreCredit, $order, $websiteId];
    }

    /**
     * Refund to store credit
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param int $orderId
     * @param CreditmemoInterface $creditmemo
     * @param int $websiteId
     * @return array
     */
    public function beforeRefundToStoreCredit(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $orderId,
        $creditmemo,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $orderId, $creditmemo, $websiteId];
    }

    /**
     * Reimbursed spent store credit
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param int $orderId
     * @param CreditmemoInterface $creditmemo
     * @param int $websiteId
     * @return array
     */
    public function beforeReimbursedSpentStoreCredit(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $orderId,
        $creditmemo,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $orderId, $creditmemo, $websiteId];
    }

    /**
     * Reimbursed spent store credit on order cancel
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param OrderInterface $order
     * @param int $websiteId
     * @return array
     */
    public function beforeReimbursedSpentStoreCreditOrderCancel(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $order,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $order, $websiteId];
    }

    /**
     * Save transaction created by admin
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param array $transactionData
     * @return array
     * @throws LocalizedException
     */
    public function beforeSaveAdminTransaction($subject, $transactionData)
    {
        $customerId = $transactionData['customer_id'];
        $this->customerStatus->checkAndEnsureCustomerStatusIsValid($customerId);
        $rootCustomer = $this->storeCreditManagement->getRootUserByCustomerId($customerId);
        if ($rootCustomer) {
            $transactionData['customer_id'] = $rootCustomer->getId();
            $transactionData['customer_name'] = $rootCustomer->getFirstname() . ' ' . $rootCustomer->getLastname();
            $transactionData['customer_email'] = $rootCustomer->getEmail();
        }
        return [$transactionData];
    }

    /**
     * Retrieve customer store credit details
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param \Closure $proceed
     * @param int $customerId
     * @return \Aheadworks\StoreCredit\Api\Data\CustomerStoreCreditDetailsInterface
     */
    public function aroundGetCustomerStoreCreditDetails($subject, $proceed, $customerId)
    {
        $rootCustomer = $this->storeCreditManagement->getRootUserByCustomerId($customerId);
        if ($rootCustomer) {
            $customerStcDetails = $proceed($rootCustomer->getId());
            $customerStcDetails = $this->storeCreditManagement
                ->changeCustomerStcDetailsIfNeeded($customerStcDetails);
        } else {
            $customerStcDetails = $proceed($customerId);
        }

        return $customerStcDetails;
    }
}
