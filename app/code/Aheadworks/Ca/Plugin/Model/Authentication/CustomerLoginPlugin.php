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

namespace Aheadworks\Ca\Plugin\Model\Authentication;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class CustomerLoginPlugin
{
    /**
     * @param SellerCompanyManagementInterface $companyManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        private readonly SellerCompanyManagementInterface $companyManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $config,
    ) {
    }

    /**
     * Check if company or user is allowed
     *
     * @param AuthenticationInterface $subject
     * @param int $customerId
     * @return array|null
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeIsLocked(AuthenticationInterface $subject, int $customerId): ?array
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $websiteId = (int)$this->storeManager->getWebsite()->getId();
        } catch (\Exception $e) {
            return [$customerId];
        }

        /** @var CompanyUserInterface $companyUser */
        $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        if ($companyUser) {
            if (!$this->config->isExtensionEnabled($websiteId)
                || $this->companyManagement->isBlockedCompany($companyUser->getCompanyId())
                || $companyUser->getStatus() == Status::INACTIVE
            ) {
                throw new LocalizedException(__('This account is blocked.'));
            }

            if ($companyUser->getStatus() == Status::PENDING) {
                throw new LocalizedException(__('The account is pending by company admin.'));
            }
        }

        return null;
    }
}
