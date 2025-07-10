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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Plugin\Controller\Profile;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Model\ProfileRepositoryFactory;
use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Controller\Profile\AbstractProfile;
use Magento\Customer\Model\Session;

/**
 * Class AbstractProfilePlugin
 */
class AbstractProfilePlugin
{
    /**
     * @var Session
     */
    private $customerSession;

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
     * @param Session $customerSession
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ProfileRepositoryFactory $profileRepositoryFactory
     */
    public function __construct(
        Session $customerSession,
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement,
        ProfileRepositoryFactory $profileRepositoryFactory
    ) {
        $this->customerSession = $customerSession;
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
        $this->profileRepositoryFactory = $profileRepositoryFactory;
    }

    /**
     * Check if profile belongs to current customer or company
     *
     * @param AbstractProfile $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsProfileBelongsToCustomer(AbstractProfile $subject, bool $result): bool
    {
        if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Sarp2::company_subscriptions_edit')) {
            return $this->isCustomerBelongsToCompany($subject) ? true : $result;
        }

        return $result;
    }

    /**
     * Check if customer belongs to company
     *
     * @param AbstractProfile $subject
     * @return bool
     */
    private function isCustomerBelongsToCompany(AbstractProfile $subject): bool
    {
        $profileId = (int)$subject->getRequest()->getParam(ProfileInterface::PROFILE_ID);
        $profileRepository = $this->profileRepositoryFactory->create();
        $profileEntity = $profileRepository->get($profileId);
        return in_array($this->customerSession->getCustomerId(), $this->companyUserManagement->getChildUsersIds($profileEntity->getCustomerId()), true);
    }
}
