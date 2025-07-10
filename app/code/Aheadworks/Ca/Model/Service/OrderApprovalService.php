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
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;
use Aheadworks\Ca\Api\OrderApprovalManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Role\OrderApproval\OrderManager;
use Aheadworks\Ca\Model\Config;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

/**
 * Class OrderApprovalService
 *
 * @package Aheadworks\Ca\Model\Service
 */
class OrderApprovalService implements OrderApprovalManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var IsActiveChecker
     */
    private $isActiveChecker;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @var OrderManager
     */
    private $orderManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param IsActiveChecker $isActiveChecker
     * @param CompanyUserProvider $companyUserProvider
     * @param OrderManager $orderManager
     * @param Config $config
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CompanyUserManagementInterface $companyUserManagement,
        IsActiveChecker $isActiveChecker,
        CompanyUserProvider $companyUserProvider,
        OrderManager $orderManager,
        Config $config
    ) {
        $this->cartRepository = $cartRepository;
        $this->companyUserManagement = $companyUserManagement;
        $this->isActiveChecker = $isActiveChecker;
        $this->companyUserProvider = $companyUserProvider;
        $this->orderManager = $orderManager;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function isApproveRequiredForCart($cartId)
    {
        $result = false;
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        if (!$this->config->isOrderApprovalEnabled($quote->getStore()->getWebsiteId())
            || $quote->getCustomerIsGuest() || !$quote->getCustomerId()
        ) {
            return $result;
        }

        $currentCompanyUser = $this->companyUserProvider->getCurrentCompanyUser();
        if ($currentCompanyUser && $currentCompanyUser->getCustomerId() == $quote->getCustomerId()) {
            $result = $this->isActiveChecker->checkForQuote($currentCompanyUser->getCompanyRoleId(), $quote);
        }

        return $result;
    }

    /**
     * @inheritdoc
     *
     * @param Order $order
     */
    public function isApproveRequiredForOrder($order)
    {
        $result = false;
        if (!$this->config->isOrderApprovalEnabled($order->getStore()->getWebsiteId())
            || $order->getCustomerIsGuest() || !$order->getCustomerId()
        ) {
            return $result;
        }

        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($order->getCustomerId());
        if ($companyUser) {
            $result = $this->isActiveChecker->checkForOrder($companyUser->getCompanyRoleId(), $order);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function approveOrder($order)
    {
        if ($this->canProcessOrder($order)) {
            $this->orderManager->approveOrder($order);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function rejectOrder($order)
    {
        if ($this->canProcessOrder($order)) {
            $this->orderManager->rejectOrder($order);
        }

        return true;
    }

    /**
     * Can process order
     *
     * @param OrderInterface|Order $order
     * @return bool
     * @throws LocalizedException
     */
    private function canProcessOrder($order)
    {
        $companyUser = $this->companyUserProvider->getCurrentCompanyUser();
        if ($companyUser && $companyUser->getIsRoot()) {
            $companyUserIds = $this->companyUserManagement->getAllUsersIdsForCompany($companyUser->getCompanyId());
            if (!in_array($order->getCustomerId(), $companyUserIds)) {
                throw new LocalizedException(__('Order doesn\'t belong to current user company'));
            }

            return true;
        }

        throw new LocalizedException(__('Current user is not a company admin'));
    }
}
