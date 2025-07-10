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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ShippingManagement
 */
class ShippingManagement
{
    /**
     * @var CompanyUserManagementInterface
     */
    private CompanyUserManagementInterface $companyUserManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    private CompanyRepositoryInterface $companyRepository;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Retrieve allowed company shipping methods
     *
     * @return array
     */
    public function getAllowedCompanyShippingMethods(): array
    {
        $allowedShippingMethods = [];
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
            try {
                $company = $this->companyRepository->get($companyUser->getCompanyId());
                $allowedShippingMethods = $company->getAllowedShippingMethods();
                // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            } catch (NoSuchEntityException $exception) {
                //returns empty array of shipping methods
            }
        }

        return $allowedShippingMethods;
    }
}
