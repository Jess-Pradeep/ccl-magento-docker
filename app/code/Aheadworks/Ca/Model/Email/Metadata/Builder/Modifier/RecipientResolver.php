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
namespace Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Magento\ModuleUser\UserRepository;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class RecipientResolver
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier
 */
class RecipientResolver
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserRepository $userRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserRepository $userRepository,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Retrieve sales representative name
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function resolveSalesRepresentativeName($company)
    {
        return $this->getBackendUserName($company->getSalesRepresentativeId());
    }

    /**
     * Resolve sales representative email
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function resolveSalesRepresentativeEmail($company)
    {
        return $this->getBackendUserEmail($company->getSalesRepresentativeId());
    }

    /**
     * Retrieve company admin name
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function resolveCompanyAdminName($company)
    {
        $customer = $this->companyUserManagement->getRootUserForCompany($company->getId());
        return $this->resolveCustomerName($customer->getId());
    }

    /**
     * Resolve company admin email
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function resolveCompanyAdminEmail($company)
    {
        $customer = $this->companyUserManagement->getRootUserForCompany($company->getId());
        return $this->resolveCustomerEmail($customer->getId());
    }

    /**
     * Resolve order owner name
     *
     * @param OrderInterface $order
     * @return string
     */
    public function resolveOrderOwnerName($order)
    {
        return $this->resolveCustomerName($order->getCustomerId());
    }

    /**
     * Resolve order owner email
     *
     * @param OrderInterface $order
     * @return string
     */
    public function resolveOrderOwnerEmail($order)
    {
        return $this->resolveCustomerEmail($order->getCustomerId());
    }

    /**
     * Get customer name
     *
     * @param int $customerId
     * @return string
     */
    public function resolveCustomerName($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $name = $customer->getFirstName() . ' ' .  $customer->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }

        return $name;
    }

    /**
     * Get customer email
     *
     * @param int $customerId
     * @return string
     */
    public function resolveCustomerEmail($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $email = $customer->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }

    /**
     * Get backend user name
     *
     * @param int $userId
     * @return string
     */
    private function getBackendUserName($userId)
    {
        try {
            $user = $this->userRepository->getById($userId);
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }

        return $name;
    }

    /**
     * Get backend user name
     *
     * @param int $userId
     * @return string
     */
    private function getBackendUserEmail($userId)
    {
        try {
            $user = $this->userRepository->getById($userId);
            $email = $user->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }
}
