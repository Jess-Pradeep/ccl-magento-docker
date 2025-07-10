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

namespace Aheadworks\Ca\ViewModel\Company;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;

class Customer implements ArgumentInterface
{
    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CompanyAdminCandidateManagementInterface $candidateManagement
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly CompanyAdminCandidateManagementInterface $candidateManagement
    ) {
    }

    /**
     * Get current company user
     *
     * @return CustomerInterface|null
     */
    public function getCurrentCompanyUser(): ?CustomerInterface
    {
        return $this->companyUserManagement->getCurrentUser();
    }

    /**
     * Get root company user
     *
     * @param int $companyId
     * @return CustomerInterface
     */
    public function getRootCompanyUser(int $companyId): CustomerInterface
    {
        return $this->companyUserManagement->getRootUserForCompany($companyId);
    }

    /**
     * Check whether current user is company admin
     *
     * @return bool
     */
    public function isCurrentUserCompanyAdmin(): bool
    {
        $user = $this->getCurrentCompanyUser();
        return $user && $user->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
    }

    /**
     * Check if company admin can be changed
     *
     * @return bool
     */
    public function isAdminChangingAllowed(): bool
    {
        $user = $this->getCurrentCompanyUser();
        return $this->isCurrentUserCompanyAdmin()
            && !$this->candidateManagement->isApproveRequired(
                $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId()
            );
    }
}
