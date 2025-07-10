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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Service;

use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderService
 */
class OrderService
{
    /**
     * OrderManagement constructor.
     *
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     * @param InvoiceResource $invoiceResource
     */
    public function __construct(
        private InvoiceService $invoiceService,
        private Transaction $transaction,
        private InvoiceResource $invoiceResource,
    ) {
    }

    /**
     * Create invoice by order
     *
     * @param OrderInterface $order
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoiceByOrder(OrderInterface $order): void
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $this->invoiceResource->save($invoice);

            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();

            $order->addCommentToStatusHistory(
                __('Notified customer about invoice creation #%1.', $invoice->getId())
            )->setIsCustomerNotified(true)->save();
        }
    }
}
