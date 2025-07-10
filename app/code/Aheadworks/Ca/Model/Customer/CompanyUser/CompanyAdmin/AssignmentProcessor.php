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

namespace Aheadworks\Ca\Model\Customer\CompanyUser\CompanyAdmin;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResource;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Customer\Model\Config\Share as ShareConfig;

class AssignmentProcessor
{
    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyUserResource $companyUserResource
     * @param RoleRepositoryInterface $roleRepository
     * @param ShareConfig $shareConfig
     * @param AssignmentProcessorInterface[] $processors
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyUserResource $companyUserResource,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly ShareConfig $shareConfig,
        private readonly array $processors = []
    ) {
    }

    /**
     * Replace company admin
     *
     * @param CustomerInterface $oldCompanyAdmin
     * @param CustomerInterface $newCompanyAdmin
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function replaceCompanyAdmin(CustomerInterface $oldCompanyAdmin, CustomerInterface $newCompanyAdmin): bool
    {
        if (!$this->shareConfig->isGlobalScope()
            && $newCompanyAdmin->getWebsiteId() != $oldCompanyAdmin->getWebsiteId()
        ) {
            throw new LocalizedException(__('Provided user is registered on different website'));
        }

        /** @var CompanyUserInterface $oldCompanyUser */
        $oldCompanyUser = $oldCompanyAdmin->getExtensionAttributes()->getAwCaCompanyUser();
        /** @var CompanyUserInterface $newCompanyUser */
        $newCompanyUser = $newCompanyAdmin->getExtensionAttributes()->getAwCaCompanyUser();
        $newCompanyUser
            ->setCompanyGroupId($oldCompanyUser->getCompanyGroupId())
            ->setCompanyId($oldCompanyUser->getCompanyId())
            ->setIsRoot(true)
            ->setCompanyRoleId($oldCompanyUser->getCompanyRoleId());

        $newCompanyAdmin->setGroupId($oldCompanyAdmin->getGroupId());

        $defaultUserRole = $this->roleRepository->getDefaultUserRole($oldCompanyUser->getCompanyId());
        $oldCompanyUser
            ->setIsRoot(false)
            ->setCompanyRoleId($defaultUserRole->getId());

        try {
            $this->companyUserResource->beginTransaction();

            foreach ($this->processors as $processor) {
                if (!$processor instanceof AssignmentProcessorInterface) {
                    throw new \InvalidArgumentException(
                        sprintf('Provided processor must implement %s', AssignmentProcessorInterface::class)
                    );
                }
                $processor->process($oldCompanyAdmin, $newCompanyAdmin);
            }

            $this->customerRepository->save($oldCompanyAdmin);
            $this->customerRepository->save($newCompanyAdmin);

            $this->companyUserResource->commit();
        } catch (\Exception $e) {
            $this->companyUserResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }
}
