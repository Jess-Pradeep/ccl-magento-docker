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

namespace Aheadworks\Ca\Model\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Api\GroupManagementInterface;
use Aheadworks\Ca\Api\RoleManagementInterface;
use Aheadworks\Ca\Model\Company\Builder as CompanyBuilder;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\Source\Company\Status;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Api\UnitManagementInterface;

class CompanyManagement
{
    /**
     * CompanyManagement Construct
     *
     * @param CompanyRepositoryInterface $companyRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param Company $resourceModel
     * @param GroupManagementInterface $groupManagement
     * @param RoleManagementInterface $roleManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param Notifier $notifier
     * @param ExtensionAttributesBuilder $companyUserExtensionAttributesBuilder
     * @param CompanyBuilder $companyBuilder
     * @param UnitManagementInterface $unitManagement
     */
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Company $resourceModel,
        private readonly GroupManagementInterface $groupManagement,
        private readonly RoleManagementInterface $roleManagement,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly Notifier $notifier,
        private readonly ExtensionAttributesBuilder $companyUserExtensionAttributesBuilder,
        private readonly CompanyBuilder $companyBuilder,
        private readonly UnitManagementInterface $unitManagement
    ) {
    }

    /**
     * Create company
     *
     * @param CompanyInterface $company
     * @param CustomerInterface $customer
     * @return CompanyInterface
     * @throws Exception
     */
    public function createCompany(CompanyInterface $company, CustomerInterface $customer)
    {
        $this->resourceModel->beginTransaction();
        try {
            // create root group
            $group = $this->groupManagement->createDefaultGroup();

            $this->companyBuilder->create($company, $customer);

            // create company
            $company->setRootGroupId($group->getId());
            $this->companyRepository->save($company);

            // create default role
            $rootRole = $this->roleManagement->createDefaultRole($company->getId());

            // create default unit
            $rootUnit = $this->unitManagement->createDefaultUnit($company->getId());

            // create default user role
            $userRole = $this->roleManagement->createDefaultUserRole($company->getId());

            // create root customer
            $customer = $this->createAndSaveDefaultCustomer(
                $customer,
                $company,
                $group,
                $rootRole,
                $rootUnit
            );

            $this->resourceModel->commit();
        } catch (Exception $e) {
            $this->resourceModel->rollBack();
            throw $e;
        }

        if ($company->getStatus() == Status::PENDING_APPROVAL) {
            $this->notifier->notifyOnNewCompanyCreated($company);
        } else {
            $this->notifier->notifyOnCompanyStatusChange($company, null);
        }

        return $company;
    }

    /**
     * Update company
     *
     * @param CompanyInterface $company
     * @param CustomerInterface $customer
     * @return CompanyInterface
     * @throws Exception
     */
    public function updateCompany(CompanyInterface $company, CustomerInterface $customer)
    {
        $this->resourceModel->beginTransaction();
        try {
            $this->companyUserManagement->saveUser($customer);
            $oldCompany = $this->companyRepository->get($company->getId());
            $this->companyRepository->save($company);
            $this->resourceModel->commit();

            $this->notifier->notifyOnCompanyStatusChange($company, $oldCompany->getStatus());
            $this->updateGroupForAllCompanyUsers($company, $oldCompany);
        } catch (Exception $e) {
            $this->resourceModel->rollBack();
            throw $e;
        }

        return $company;
    }

    /**
     * Check if company blocked
     *
     * @param int $companyId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isBlocked($companyId)
    {
        $company = $this->companyRepository->get($companyId);

        return $company->getStatus() != Status::APPROVED;
    }

    /**
     * Change company status
     *
     * @param int $companyId
     * @param string $status
     * @return bool
     */
    public function changeStatus($companyId, $status)
    {
        try {
            $company = $this->companyRepository->get($companyId);
            $oldStatus = $company->getStatus();
            $company->setStatus($status);
            $this->companyRepository->save($company);
            $this->notifier->notifyOnCompanyStatusChange($company, $oldStatus);

            $result = true;
        } catch (\Exception $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get Company By Customer Id
     *
     * @param int|null $customerId
     * @return CompanyInterface|null
     */
    public function getCompanyByCustomerId($customerId): ?CompanyInterface
    {
        $company = null;

        if (!$customerId) {
            return $company;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $companyId = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                $company = $this->companyRepository->get($companyId);
            }
        } catch (\Exception $e) {
            $company = null;
        }

        return $company;
    }

    /**
     * Create root customer
     *
     * @param CustomerInterface $customer
     * @param CompanyInterface $company
     * @param GroupInterface $group
     * @param RoleInterface $role
     * @param UnitInterface $rootUnit
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function createAndSaveDefaultCustomer(
        CustomerInterface $customer,
        CompanyInterface $company,
        GroupInterface $group,
        RoleInterface $role,
        UnitInterface $rootUnit
    ) {
        $this->companyUserExtensionAttributesBuilder->setAwCompanyUserIfNotIsset($customer);

        $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyId($company->getId())
            ->setIsRoot(true)
            ->setCompanyGroupId($group->getId())
            ->setCompanyRoleId($role->getId())
            ->setCompanyUnitId((int)$rootUnit->getId());

        return $this->companyUserManagement->saveUser($customer);
    }

    /**
     * Update group for company users
     *
     * @param CompanyInterface $company
     * @param CompanyInterface $oldCompany
     * @return void
     */
    private function updateGroupForAllCompanyUsers($company, $oldCompany)
    {
        if ($company->getCustomerGroupId() != $oldCompany->getCustomerGroupId()) {
            $groupId = $company->getCustomerGroupId();

            $customers = $this->companyUserManagement->getAllUserForCompany($company->getId());

            foreach ($customers as $customer) {
                $customer->setGroupId($groupId);
                try {
                    $this->companyUserManagement->saveUser($customer);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
}
