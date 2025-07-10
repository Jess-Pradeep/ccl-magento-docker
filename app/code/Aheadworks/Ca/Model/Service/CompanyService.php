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
namespace Aheadworks\Ca\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ca\Api\CompanyManagementInterface;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResource;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Company\Checker\IsAllowedToRemoveCompany;

/**
 * Class CompanyService
 *
 * @package Aheadworks\Ca\Model\Service
 */
class CompanyService implements CompanyManagementInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CompanyResource
     */
    private $companyResource;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var IsAllowedToRemoveCompany
     */
    private $isAllowedToRemoveCompany;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyResource $companyResource
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param IsAllowedToRemoveCompany $isAllowedToRemoveCompany
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CompanyResource $companyResource,
        CompanyRepositoryInterface $companyRepository,
        CompanyUserManagementInterface $companyUserManagement,
        IsAllowedToRemoveCompany $isAllowedToRemoveCompany
    ) {
        $this->customerRepository = $customerRepository;
        $this->companyResource = $companyResource;
        $this->companyRepository = $companyRepository;
        $this->companyUserManagement = $companyUserManagement;
        $this->isAllowedToRemoveCompany = $isAllowedToRemoveCompany;
    }

    /**
     * @inheritdoc
     */
    public function removeCompany($companyId)
    {
        try {
            $company = $this->companyRepository->get($companyId);
            if (!$this->isAllowedToRemoveCompany->check($company)) {
                return false;
            }

            $this->companyResource->beginTransaction();
            $companyUserIds = $this->companyUserManagement->getAllUsersIdsForCompany($companyId);
            $this->companyRepository->delete($company);
            foreach ($companyUserIds as $userId) {
                $this->customerRepository->deleteById($userId);
            }

            $this->companyResource->commit();
        } catch (\Exception $e) {
            $this->companyResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }
}
