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
namespace Aheadworks\CreditLimit\Plugin\Sales\Model\Order;

use Magento\Sales\Model\Order\Invoice;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;

/**
 * Class InvoicePlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Sales\Model\Order
 */
class InvoicePlugin
{
    /**
     * @var CreditLimitManagementInterface
     */
    private $creditManagement;

    /**
     * @param CreditLimitManagementInterface $creditManagement
     */
    public function __construct(
        CreditLimitManagementInterface $creditManagement
    ) {
        $this->creditManagement = $creditManagement;
    }

    /**
     * Increase customer credit balance by buying balance unit product
     *
     * @param Invoice $object
     * @param Invoice $invoice
     * @return Invoice
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Invoice $object, Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getCustomerId()) {
            $this->creditManagement->increaseCreditBalanceByUnitPurchase($order->getCustomerId(), $invoice);
        }

        return $invoice;
    }
}
