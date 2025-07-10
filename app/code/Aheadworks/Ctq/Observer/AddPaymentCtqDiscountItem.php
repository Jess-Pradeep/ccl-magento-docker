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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\Cart;
use Magento\Payment\Model\Cart\SalesModel\SalesModelInterface;
use Aheadworks\Ctq\Api\Data\CartInterface;

/**
 * Class AddPaymentCtqDiscountItem
 *
 * @package Aheadworks\Ctq\Observer
 */
class AddPaymentCtqDiscountItem implements ObserverInterface
{
    /**
     * Add base ctq discount amount to payment discount total
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();
        /** @var SalesModelInterface $salesModel */
        $salesModel = $cart->getSalesModel();
        $baseAwCtqAmount = abs((float)$salesModel->getDataUsingMethod(CartInterface::BASE_AW_CTQ_AMOUNT));
        if ($baseAwCtqAmount > 0.0001) {
            $cart->addDiscount((double)$baseAwCtqAmount);
        }
    }
}
