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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\ThirdPartyModule\Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Service\CompanyUserService;
use Aheadworks\CreditLimit\Model\ThirdPartyModule\Manager as ThirdPartyModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\DataObject;

/**
 * Class CompanyManagementService
 */
class CompanyManagementService
{
    public function __construct(
        private ThirdPartyModuleManager $thirdPartyModuleManager,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * Is customer in company
     *
     * @param string|int $customerId
     * @return bool
     */
    public function isCustomerInCompany($customerId): bool
    {
        $result = false;
        if ($this->thirdPartyModuleManager->isAwCaModuleEnabled() && class_exists(CompanyUserProvider::class)) {
            $companyUserProvider = $this->objectManager->get(CompanyUserProvider::class);
            $result = (bool)$companyUserProvider->getCompanyUserByCustomer($customerId);
        }
        return $result;
    }

    /**
     * Get root user of company by company id
     *
     * @param string|int $companyId
     * @return CustomerInterface|null
     */
    public function getRootUserByCompanyId($companyId): ?CustomerInterface
    {
        $result = null;
        if ($this->thirdPartyModuleManager->isAwCaModuleEnabled() && class_exists(CompanyUserService::class)) {
            $companyUserService = $this->objectManager->get(CompanyUserService::class);
            $rootUser = $companyUserService->getRootUserForCompany($companyId);
            if ($rootUser !== false) {
                $result = $rootUser;
            }
        }
        return $result;
    }

    /**
     * Get company by email
     *
     * @param string $email
     * @return DataObject|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCompanyByEmail(string $email): ?DataObject
    {
        $result = null;
        if ($this->thirdPartyModuleManager->isAwCaModuleEnabled() &&
            interface_exists(CompanyInterface::class) &&
            interface_exists(CompanyRepositoryInterface::class)) {
            $this->searchCriteriaBuilder->addFilter(CompanyInterface::EMAIL, $email);
            $companyRepository = $this->objectManager->get(CompanyRepositoryInterface::class);
            $companyList = $companyRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            if ($companyList) {
                $result = reset($companyList);
            }
        }
        return $result;
    }
}
