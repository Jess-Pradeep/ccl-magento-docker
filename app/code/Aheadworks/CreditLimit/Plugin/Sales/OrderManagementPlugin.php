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

namespace Aheadworks\CreditLimit\Plugin\Sales;

use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Model\Service\OrderService;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;

/**
 * Class OrderManagementPlugin
 */
class OrderManagementPlugin
{
    /**
     * OrderManagementPlugin constructor.
     *
     * @param Config $config
     * @param OrderService $orderService
     * @param PaymentPeriodManagementInterface $paymentPeriodService
     */
    public function __construct(
        private Config $config,
        private OrderService $orderService,
        private PaymentPeriodManagementInterface $paymentPeriodService
    ) {
    }

    /**
     * Create credit limit order invoice after place
     *
     * @param OrderManagementInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $order
    ): OrderInterface {
        $isGenerateInvoice = $this->config->isEnableAutoGenerateInvoice($order->getStoreId());
        if ($order->getPayment() && $order->getPayment()->getMethod() === 'aw_credit_limit') {
            $this->createDueDate($order);
            if ($isGenerateInvoice) {
                $this->orderService->createInvoiceByOrder($order);
            }
        }

        return $order;
    }

    /**
     * Calc and set due date after first order placed with credit limit
     *
     * @param OrderInterface $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createDueDate(OrderInterface $order): void
    {
        $customerId = (int)$order->getCustomerId();
        if ($customerId) {
            $hasDueDate = (bool)$this->paymentPeriodService->getDueDate($customerId);
            if (!$hasDueDate) {
                $dueDate = $this->paymentPeriodService->getCalcDueDate($customerId, $order->getCreatedAt());
                if ($dueDate) {
                    $this->paymentPeriodService->updateDueDate($dueDate, $customerId);
                }
            }
        }
    }
}
