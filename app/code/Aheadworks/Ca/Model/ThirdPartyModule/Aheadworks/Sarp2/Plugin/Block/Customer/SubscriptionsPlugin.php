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
use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Model\ResourceModel\Profile\Collection;
use Magento\Customer\Model\Session;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Model\ResourceModel\ProfileCollectionFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class SubscriptionsPlugin
 */
class SubscriptionsPlugin
{
    /**
     * @var Collection
     */
    private $collection;

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
     * @var ProfileCollectionFactory
     */
    private $profileCollectionFactory;

    /**
     * @param Session $customerSession
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ProfileCollectionFactory $profileCollectionFactory
     */
    public function __construct(
        Session $customerSession,
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement,
        ProfileCollectionFactory $profileCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
        $this->profileCollectionFactory = $profileCollectionFactory;
    }

    /**
     * Check current logged in customer and retrieve subscriptions
     *
     * @param ArgumentInterface $subject
     * @param Collection $collection
     * @return Collection|null
     */
    public function afterGetProfiles(ArgumentInterface $subject, Collection $collection): ?Collection
    {
        if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Sarp2::company_subscriptions_view')) {
            $customerId = $this->customerSession->getCustomerId();
            $customers = $this->companyUserManagement->getChildUsersIds($customerId);
            $customers[] = $customerId;
            $this->collection = $this->profileCollectionFactory->create();
            $this->collection
                ->addFieldToFilter(
                    ProfileInterface::CUSTOMER_ID,
                    ['in' => $customers]
                )
                ->addOrder(ProfileInterface::CREATED_AT, Collection::SORT_ORDER_DESC);
            $collection = $this->collection;
        }

        return $collection;
    }
}
