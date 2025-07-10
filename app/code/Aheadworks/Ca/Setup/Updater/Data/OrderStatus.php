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
namespace Aheadworks\Ca\Setup\Updater\Data;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\OrderStatus as OrderStatusSource;

class OrderStatus
{
    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    private $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    private $statusResourceFactory;

    /**
     * @var OrderStatusSource
     */
    private $orderStatusSource;

    /**
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param OrderStatusSource $orderStatusSource
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        OrderStatusSource $orderStatusSource
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->orderStatusSource = $orderStatusSource;
    }

    /**
     * Install new order statuses
     *
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup)
    {
        $this->addPendingStatus();
        $this->addRejectedStatus();
    }

    /**
     * Add pending status
     *
     * @throws \Exception
     */
    private function addPendingStatus()
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $pendingStatus = $this->statusFactory->create();
        $pendingStatus->setData($this->orderStatusSource->getPendingStatus());
        try {
            $statusResource->save($pendingStatus);
            $pendingStatus->assignState(Order::STATE_PROCESSING, false, true);
        } catch (AlreadyExistsException $exception) {
            return;
        }
    }

    /**
     * Add rejected status
     *
     * @throws \Exception
     */
    private function addRejectedStatus()
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $rejectedStatus = $this->statusFactory->create();
        $rejectedStatus->setData($this->orderStatusSource->getRejectedStatus());
        try {
            $statusResource->save($rejectedStatus);
            $rejectedStatus->assignState(Order::STATE_CANCELED, false, true);
        } catch (AlreadyExistsException $exception) {
            return;
        }
    }
}
