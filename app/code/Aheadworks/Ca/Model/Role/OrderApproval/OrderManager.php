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
namespace Aheadworks\Ca\Model\Role\OrderApproval;

use Psr\Log\LoggerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\OrderStatus;
use Aheadworks\Ca\Model\ResourceModel\Role\OrderApproval\State as StateResourceModel;
use Aheadworks\Ca\Model\Role\OrderApproval\OrderManager\RejectProcessor;

/**
 * Class OrderManager
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
class OrderManager
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StateInterfaceFactory
     */
    private $stateFactory;

    /**
     * @var StateResourceModel
     */
    private $stateResourceModel;

    /**
     * @var OrderValidator
     */
    private $orderValidator;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RejectProcessor
     */
    private $rejectProcessor;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param LoggerInterface $logger
     * @param StateResourceModel $stateResourceModel
     * @param StateInterfaceFactory $stateFactory
     * @param OrderValidator $orderValidator
     * @param OrderRepositoryInterface $orderRepository
     * @param RejectProcessor $rejectProcessor
     * @param Notifier $notifier
     */
    public function __construct(
        LoggerInterface $logger,
        StateResourceModel $stateResourceModel,
        StateInterfaceFactory $stateFactory,
        OrderValidator $orderValidator,
        OrderRepositoryInterface $orderRepository,
        RejectProcessor $rejectProcessor,
        Notifier $notifier
    ) {
        $this->logger = $logger;
        $this->stateResourceModel = $stateResourceModel;
        $this->stateFactory = $stateFactory;
        $this->orderValidator = $orderValidator;
        $this->orderRepository = $orderRepository;
        $this->rejectProcessor = $rejectProcessor;
        $this->notifier = $notifier;
    }

    /**
     * Apply order approval
     *
     * @param OrderInterface|Order $order
     * @return bool
     * @throws LocalizedException
     */
    public function applyOrderApproval(OrderInterface $order)
    {
        if (!$this->orderValidator->validateToApplyApproval($order)) {
            $messages = $this->orderValidator->getMessages();
            foreach ($messages as $message) {
                $this->logger->error($message);
            }
            return false;
        }

        /** @var StateInterface|AbstractModel $state */
        $state = $this->stateFactory->create();
        $state
            ->setOrderId($order->getEntityId())
            ->setInitialOrderStatus($order->getStatus())
            ->setInitialOrderState($order->getState());

        $this->stateResourceModel->save($state);
        $order->setStatus(OrderStatus::PENDING_APPROVAL);
        $order->addStatusHistoryComment(__('Order was sent for approval to company admin'));
        $this->orderRepository->save($order);
        $this->notifier->notify($order, Notifier::ORDER_WAS_SENT_FOR_APPROVAL);

        return true;
    }

    /**
     * Approve order
     *
     * @param OrderInterface|Order $order
     * @return bool
     * @throws LocalizedException
     * @throws \Exception
     */
    public function approveOrder(OrderInterface $order)
    {
        if (!$this->orderValidator->validateToApprove($order)) {
            $messages = $this->orderValidator->getMessages();
            throw new LocalizedException(reset($messages));
        }

        $stateId = $this->stateResourceModel->getStateIdByOrderId($order->getEntityId());
        $state = $this->loadState($stateId);

        $order->setStatus($state->getInitialOrderStatus());
        $order->addStatusHistoryComment(__('The order was approved'));
        $this->orderRepository->save($order);
        $this->notifier->notify($order, Notifier::ORDER_STATUS_CHANGED);

        return true;
    }

    /**
     * Reject order
     *
     * @param OrderInterface|Order $order
     * @return bool
     * @throws LocalizedException
     * @throws \Exception
     */
    public function rejectOrder(OrderInterface $order)
    {
        if (!$this->orderValidator->validateToReject($order)) {
            $messages = $this->orderValidator->getMessages();
            throw new LocalizedException(reset($messages));
        }

        $this->rejectProcessor->process($order);
        $order->setStatus(OrderStatus::REJECTED);
        $order->addStatusHistoryComment(__('The order was rejected'));
        $this->orderRepository->save($order);
        $this->notifier->notify($order, Notifier::ORDER_STATUS_CHANGED);

        return true;
    }

    /**
     * Load order approval state
     *
     * @param int $stateId
     * @return StateInterface|AbstractModel
     */
    private function loadState($stateId)
    {
        /** @var StateInterface|AbstractModel $state */
        $state = $this->stateFactory->create();
        $this->stateResourceModel->load($state, $stateId);

        return $state;
    }
}
