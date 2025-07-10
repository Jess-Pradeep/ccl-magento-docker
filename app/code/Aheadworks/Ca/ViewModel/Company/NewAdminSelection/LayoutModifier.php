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

namespace Aheadworks\Ca\ViewModel\Company\NewAdminSelection;

use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\ArrayManager;

class LayoutModifier implements ArgumentInterface
{
    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CompanyAdminCandidateManagementInterface $companyAdminCandidateManagement
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly CompanyAdminCandidateManagementInterface $companyAdminCandidateManagement,
        private readonly ArrayManager $arrayManager,
    ) {
    }

    /**
     * Modify layout
     *
     * @param array $layout
     * @return array
     */
    public function modify(array $layout): array
    {
        $user = $this->companyUserManagement->getCurrentUser();
        if ($user && $user->getExtensionAttributes()->getAwCaCompanyUser()) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
            $providerPath = $this->arrayManager->findPath('awCaCompanyAdminSelectionProvider', $layout);
            if ($providerPath) {
                $config['data']['company'] = [
                    'id' => $companyUser->getCompanyId(),
                    'is_approve_required' => $this->companyAdminCandidateManagement->isApproveRequired(
                        $companyUser->getCompanyId()
                    )
                ];
                $layout = $this->arrayManager->merge($providerPath, $layout, $config);
            }
        }

        return $layout;
    }
}
