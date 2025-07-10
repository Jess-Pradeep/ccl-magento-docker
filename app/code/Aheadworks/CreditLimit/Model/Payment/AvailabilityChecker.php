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
namespace Aheadworks\CreditLimit\Model\Payment;

use Magento\Quote\Model\Quote;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartChecker;

/**
 * Class AvailabilityChecker
 *
 * @package Aheadworks\CreditLimit\Model\Payment
 */
class AvailabilityChecker
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CartChecker
     */
    private $cartChecker;

    /**
     * @param CustomerManagementInterface $customerManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param CartChecker $cartChecker
     */
    public function __construct(
        CustomerManagementInterface $customerManagement,
        CustomerRepositoryInterface $customerRepository,
        CartChecker $cartChecker
    ) {
        $this->customerManagement = $customerManagement;
        $this->customerRepository = $customerRepository;
        $this->cartChecker = $cartChecker;
    }

    /**
     * Check if credit limit is available on frontend
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isAvailableOnFrontend($quote)
    {
        return $quote
            && $quote->getCustomerId()
            && !$this->cartChecker->isBalanceUnitFoundInQuote($quote)
            && $this->customerManagement->isCreditLimitAvailable($quote->getCustomerId())
            && $this->customerManagement->getCreditAvailableAmount($quote->getCustomerId()) != 0;
    }

    /**
     * Check if credit limit is available in backend
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isAvailableInAdmin($quote)
    {
        $isAvailable = $this->isAvailableOnFrontend($quote);
        if ($isAvailable) {
            try {
                $customer = $this->customerRepository->getById($quote->getCustomerId());
                if ($quote->getCustomerGroupId() != $customer->getGroupId()) {
                    $isAvailable = false;
                }
            } catch (\Exception $exception) {
                $isAvailable = false;
            }
        }

        return $isAvailable;
    }
}
