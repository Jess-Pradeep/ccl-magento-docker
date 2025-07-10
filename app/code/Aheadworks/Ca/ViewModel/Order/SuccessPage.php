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
namespace Aheadworks\Ca\ViewModel\Order;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\OrderStatus;

/**
 * Class SuccessPage
 *
 * @package Aheadworks\Ca\ViewModel\Order
 */
class SuccessPage implements ArgumentInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check if last session order has status company pending approval
     *
     * @return string
     */
    public function isOrderStatusCompanyPendingApproval()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return $order->getStatus() == OrderStatus::PENDING_APPROVAL;
    }

    /**
     * Get success message
     *
     * @return Phrase
     */
    public function getSuccessMessage()
    {
        return __('Your order was successfully sent for approval to company admin');
    }
}
