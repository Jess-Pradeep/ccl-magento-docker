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
namespace Aheadworks\Ca\Plugin\Model\Sales\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;
use Aheadworks\Ca\Api\OrderApprovalManagementInterface;
use Aheadworks\Ca\Model\Role\OrderApproval\OrderManager;
use Aheadworks\Ca\Model\Customer\Checker\CustomerStatus as CustomerStatusChecker;
use Magento\Sales\Model\Order;

/**
 * Class OrderServicePlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\Sales\Service
 */
class OrderServicePlugin
{
    /**
     * @var OrderApprovalManagementInterface
     */
    private $orderApprovalManagement;

    /**
     * @var OrderManager
     */
    private $orderManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var IsActiveChecker
     */
    private $isActiveChecker;

    /**
     * @var CustomerStatusChecker
     */
    private $customerStatusChecker;

    /**
     * @param OrderApprovalManagementInterface $orderApprovalManagement
     * @param OrderManager $orderManager
     * @param OrderRepositoryInterface $orderRepository
     * @param IsActiveChecker $isActiveChecker
     * @param CustomerStatusChecker $customerStatusChecker
     */
    public function __construct(
        OrderApprovalManagementInterface $orderApprovalManagement,
        OrderManager $orderManager,
        OrderRepositoryInterface $orderRepository,
        IsActiveChecker $isActiveChecker,
        CustomerStatusChecker $customerStatusChecker
    ) {
        $this->orderApprovalManagement = $orderApprovalManagement;
        $this->orderManager = $orderManager;
        $this->orderRepository = $orderRepository;
        $this->isActiveChecker = $isActiveChecker;
        $this->customerStatusChecker = $customerStatusChecker;
    }

    /**
     * Check if customer can place order
     *
     * @param OrderManagementInterface $orderService
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePlace(OrderManagementInterface $orderService, $order)
    {
        $customerId = $order->getCustomerId();
        if ($customerId) {
            $this->customerStatusChecker->checkAndEnsureCustomerStatusIsValid($customerId);
        }

        return null;
    }

    /**
     * Apply order approval
     *
     * @param OrderManagementInterface $orderService
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(OrderManagementInterface $orderService, $resultOrder)
    {
        if ($this->orderApprovalManagement->isApproveRequiredForOrder($resultOrder)) {
            $this->orderManager->applyOrderApproval($resultOrder);
        }

        return $resultOrder;
    }

    /**
     * Check if order can ba canceled
     *
     * @param OrderManagementInterface $orderService
     * @param callable $proceed
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCancel(OrderManagementInterface $orderService, callable $proceed, $orderId)
    {
        return $this->canProceed($orderId) ? $proceed($orderId) : false;
    }

    /**
     * Check if order can be put on hold
     *
     * @param OrderManagementInterface $orderService
     * @param callable $proceed
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundHold(OrderManagementInterface $orderService, callable $proceed, $orderId)
    {
        return $this->canProceed($orderId) ? $proceed($orderId) : false;
    }

    /**
     * Check if order can be released from on hold status
     *
     * @param OrderManagementInterface $orderService
     * @param callable $proceed
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundUnHold(OrderManagementInterface $orderService, callable $proceed, $orderId)
    {
        return $this->canProceed($orderId) ? $proceed($orderId) : false;
    }

    /**
     * Check if order can be processed
     *
     * @param int $orderId
     * @return bool
     */
    private function canProceed($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        return !($this->isActiveChecker->isOrderUnderApprovalConsideration($order)
            || $this->isActiveChecker->isOrderRejected($order));
    }
}
