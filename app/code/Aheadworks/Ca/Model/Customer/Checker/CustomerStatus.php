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
namespace Aheadworks\Ca\Model\Customer\Checker;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status;

/**
 * Class CustomerStatus
 *
 * @package Aheadworks\Ca\Model\Customer\Checker
 */
class CustomerStatus
{
    /**
     * @var SellerCompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param SellerCompanyManagementInterface $companyManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SellerCompanyManagementInterface $companyManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->companyManagement = $companyManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check and ensure customer status is valid to proceed
     *
     * @param int $customerId
     * @throws LocalizedException
     */
    public function checkAndEnsureCustomerStatusIsValid($customerId)
    {
        if ($this->isCustomerBlocked($customerId)) {
            throw new LocalizedException(__('This account is blocked.'));
        }
        if ($this->isCustomerNotApprovedYet($customerId)) {
            throw new LocalizedException(__('This account is not approved yet by company admin.'));
        }
    }

    /**
     * Check whether customer is blocked
     *
     * @param int $customerId
     * @return bool
     */
    public function isCustomerBlocked($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Exception $e) {
            return false;
        }

        if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            if ($this->companyManagement->isBlockedCompany($companyUser->getCompanyId())
                || $customer->getExtensionAttributes()->getAwCaCompanyUser()->getStatus() == Status::INACTIVE
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether customer is not approved yet
     *
     * @param int $customerId
     * @return bool
     */
    public function isCustomerNotApprovedYet($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Exception $e) {
            return false;
        }

        if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            return $customer->getExtensionAttributes()->getAwCaCompanyUser()->getStatus() == Status::PENDING;
        }

        return false;
    }
}
