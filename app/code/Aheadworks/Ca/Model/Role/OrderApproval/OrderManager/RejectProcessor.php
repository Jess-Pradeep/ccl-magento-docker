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
namespace Aheadworks\Ca\Model\Role\OrderApproval\OrderManager;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;

/**
 * Class RejectProcessor
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval\OrderManager
 */
class RejectProcessor
{
    /**
     * @var OrderManagementInterface
     */
    private $salesOrderManagement;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var CreditmemoFactory
     */
    private $creditMemoFactory;

    /**
     * @var CreditmemoManagementInterface
     */
    private $creditMemoManagement;

    /**
     * @param OrderManagementInterface $salesOrderManagement
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param CreditmemoFactory $creditMemoFactory
     * @param CreditmemoManagementInterface $creditMemoManagement
     */
    public function __construct(
        OrderManagementInterface $salesOrderManagement,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoFactory $creditMemoFactory,
        CreditmemoManagementInterface $creditMemoManagement
    ) {
        $this->salesOrderManagement = $salesOrderManagement;
        $this->invoiceRepository = $invoiceRepository;
        $this->creditMemoFactory = $creditMemoFactory;
        $this->creditMemoManagement = $creditMemoManagement;
    }

    /**
     * Process order rejection
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function process(OrderInterface $order)
    {
        if ($order->canCancel()) {
            return $this->salesOrderManagement->cancel($order->getId());
        }

        if ($order->canCreditmemo()) {
            return $this->issueRefund($order);
        }

        return false;
    }

    /**
     * Issue a full refund
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    private function issueRefund(OrderInterface $order)
    {
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            /** @var CreditmemoInterface $creditMemo */
            $creditMemo = $this->creditMemoFactory->createByInvoice($invoice);
            $this->creditMemoManagement->refund($creditMemo);
        }

        return true;
    }
}
