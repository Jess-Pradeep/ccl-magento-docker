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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Plugin\Block\Customer;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Block\Customer\Subscription;
use Aheadworks\Ca\Model\ResourceModel\HistoryLogFactory as HistoryLogResourceFactory;
use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\Resolver\UserResolver;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Model\ProfileRepositoryFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SubscriptionPlugin
 */
class SubscriptionPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var HistoryLogResourceFactory
     */
    private $historyLogResourceFactory;

    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var ProfileRepositoryFactory
     */
    private ProfileRepositoryFactory $profileRepositoryFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param HistoryLogResourceFactory $historyLogResourceFactory
     * @param UserResolver $userResolver
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ProfileRepositoryFactory $profileRepositoryFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        HistoryLogResourceFactory $historyLogResourceFactory,
        UserResolver $userResolver,
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement,
        ProfileRepositoryFactory $profileRepositoryFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->historyLogResourceFactory = $historyLogResourceFactory;
        $this->userResolver = $userResolver;
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
        $this->profileRepositoryFactory = $profileRepositoryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Add Updated By Value to Profile
     *
     * @param Subscription $subject
     * @param callable $proceed
     * @param ProfileInterface $profile
     * @return string
     */
    public function aroundGetUpdatedBy(Subscription $subject, callable $proceed, ProfileInterface $profile): string
    {
        $customerName = '';
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        if ($this->config->isHistoryLogEnabled($websiteId)) {
            $historyLogRecord = $this->historyLogResourceFactory->create()->getLastLogRecordByEntityId(
                (int)$profile->getProfileId(),
                'Subscription'
            );
            if (!empty($historyLogRecord)) {
                $customerName = $historyLogRecord[HistoryLogInterface::CUSTOMER_NAME];
            }
        }

        return $customerName;
    }

    /**
     * Allow Show updated by in subscriptions
     *
     * @param Subscription $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanShowUpdatedBy(Subscription $subject, bool $result): bool
    {
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        if ($this->config->isHistoryLogEnabled($websiteId)) {
            return true;
        }

        return $result;
    }

    /**
     * Check is extend action available
     *
     * @param Subscription $subject
     * @param int $profileId
     * @return bool
     */
    public function aroundIsExtendActionAvailable(Subscription $subject,  callable $proceed, int $profileId): bool
    {
        return $this->isCustomerCanEdit($profileId) ? $proceed($profileId) : false;
    }

    /**
     * Check is extend action available
     *
     * @param Subscription $subject
     * @param int $profileId
     * @return bool
     */
    public function aroundIsEditActionAvailable(Subscription $subject,  callable $proceed, int $profileId): bool
    {
        return $this->isCustomerCanEdit($profileId) ? $proceed($profileId) : false;
    }

    /**
     * Check is extend action available
     *
     * @param Subscription $subject
     * @param int $profileId
     * @return bool
     */
    public function aroundIsRenewActionAvailable(Subscription $subject,  callable $proceed, int $profileId): bool
    {
        return $this->isCustomerCanEdit($profileId) ? $proceed($profileId) : false;
    }

    /**
     * Check if customer can edit profile
     *
     * @param int $profileId
     * @return bool
     */
    public function isCustomerCanEdit(int $profileId): bool
    {
        $profileRepository = $this->profileRepositoryFactory->create();
        $profile = $profileRepository->get($profileId);
        if (($profile->getCustomerId()) && ((int)$profile->getCustomerId() === $this->userResolver->getUserId())) {
            return true;
        }
        if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Sarp2::company_subscriptions_edit')) {
            return in_array($this->userResolver->getUserId(), $this->companyUserManagement->getChildUsersIds((int)$profile->getCustomerId()));
        }

        return false;
    }
}
