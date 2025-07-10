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

namespace Aheadworks\Ca\ViewModel\User;

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status as StatusSource;
use Aheadworks\Ca\Api\UnitRepositoryInterface;

class User implements ArgumentInterface
{
    /**
     * User Construct
     *
     * @param Url $url
     * @param RoleRepositoryInterface $roleRepository
     * @param CompanyUserProvider $companyUserProvider
     * @param StatusSource $statusSource
     * @param UnitRepositoryInterface $unitRepository
     */
    public function __construct(
        private readonly Url $url,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly CompanyUserProvider $companyUserProvider,
        private readonly StatusSource $statusSource,
        private readonly UnitRepositoryInterface $unitRepository
    ) {
    }

    /**
     * Retrieve role name by role id
     *
     * @param int $roleId
     * @return string
     */
    public function getRoleName($roleId)
    {
        try {
            $role = $this->roleRepository->get($roleId);
        } catch (NoSuchEntityException $e) {
            return '';
        }

        return $role->getName();
    }

    /**
     * Retrieve unit name by unit id
     *
     * @param int $unitId
     * @return string
     */
    public function getUnitName($unitId)
    {
        try {
            $unit = $this->unitRepository->get($unitId);
        } catch (NoSuchEntityException $e) {
            return '';
        }

        return $unit->getUnitTitle();
    }

    /**
     * Get status label
     *
     * @param int $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        return $this->statusSource->getStatusLabel($status);
    }

    /**
     * Retrieve edit url
     *
     * @param int $customerId
     * @return string
     */
    public function getEditUrl($customerId)
    {
        return $this->url->getFrontendEditCustomerUrl($customerId);
    }

    /**
     * Retrieve customer change status url
     *
     * @param int $customerId
     * @param bool $needActivate
     * @return string
     */
    public function getChangeStatusUrl($customerId, $needActivate)
    {
        return $this->url->getFrontendCustomerChangeStatusUrl($customerId, $needActivate);
    }

    /**
     * Check if given customer id belong current customer
     *
     * @param int $customerId
     * @return bool
     */
    public function isCurrentCompanyUser($customerId)
    {
        $currentUser = $this->companyUserProvider->getCurrentCompanyUser();
        return $currentUser->getId() == $customerId;
    }

    /**
     * Check if customer is root
     *
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isRoot($customer)
    {
        return (bool)$customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
    }

    /**
     * Check if customer is activated
     *
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isActivated($customer)
    {
        return $customer->getExtensionAttributes()->getAwCaCompanyUser()->getStatus() == StatusSource::ACTIVE;
    }

    /**
     * Check if change status button is visible
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    public function isChangeStatusButtonVisible($customer)
    {
        $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        if ($companyUser->getStatus() == StatusSource::PENDING) {
            return $this->companyUserProvider->isCurrentCompanyUserRoot();
        }

        return !$this->isCurrentCompanyUser($customer->getId()) && !$this->isRoot($customer);
    }
}
