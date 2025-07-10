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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class Provider
{
    /**
     * @param CompanyUserManagementInterface $userManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $userManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly AddressRepositoryInterface $addressRepository
    ) {
    }

    /**
     * Get current company user
     *
     * @return CompanyUser|null
     */
    public function getCurrentCompanyUser()
    {
        $user = $this->userManagement->getCurrentUser();
        if ($user) {
            return $this->getCompanyUser($user);
        }

        return null;
    }

    /**
     * Check if current company user is root
     *
     * @return bool
     */
    public function isCurrentCompanyUserRoot()
    {
        $companyUser = $this->getCurrentCompanyUser();
        return $companyUser && $companyUser->getIsRoot();
    }

    /**
     * Get company user by customer
     *
     * @param int $customerId
     * @return CompanyUser|null
     */
    public function getCompanyUserByCustomer($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $result = $this->getCompanyUser($customer);
        } catch (\Exception $exception) {
            $result = null;
        }

        return $result;
    }


    /**
     * Check if customer is root
     *
     * @param int $customerId
     * @return bool
     */
    public function isCustomerRoot($customerId)
    {
        $result = false;
        $companyUser = $this->getCompanyUserByCustomer($customerId);
        if ($companyUser) {
            $result = $companyUser->getIsRoot();
        }

        return $result;
    }

    /**
     * Get current company user
     *
     * @param CustomerInterface $customer
     * @return CompanyUser|bool
     */
    private function getCompanyUser($customer)
    {
        $result = false;
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $result = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        }

        return $result;
    }

    /**
     * Get company user addresses
     *
     * @param int $companyUserId
     * @return array|null
     * @throws LocalizedException
     */
    public function getCompanyUserAddresses(int $companyUserId): ?array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('parent_id', $companyUserId)
            ->create();

        return $this->addressRepository->getList($searchCriteria)->getItems();
    }
}
