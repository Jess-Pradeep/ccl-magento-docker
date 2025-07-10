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
namespace Aheadworks\Ca\ViewModel\Multishipping;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Multishipping\Model\Checkout\Type\Multishipping;
use Aheadworks\Ca\Api\OrderApprovalManagementInterface;

/**
 * Class ApprovalNotice
 *
 * @package Aheadworks\Ca\ViewModel\Multishipping
 */
class ApprovalNotice implements ArgumentInterface
{
    /**
     * @var Multishipping
     */
    private $multishippingCheckout;

    /**
     * @var OrderApprovalManagementInterface
     */
    private $orderApprovalManagement;

    /**
     * @param Multishipping $multishippingCheckout
     * @param OrderApprovalManagementInterface $orderApprovalManagement
     */
    public function __construct(
        Multishipping $multishippingCheckout,
        OrderApprovalManagementInterface $orderApprovalManagement
    ) {
        $this->multishippingCheckout = $multishippingCheckout;
        $this->orderApprovalManagement = $orderApprovalManagement;
    }

    /**
     * Check whether notice is visible
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isNoticeVisible()
    {
        $quote = $this->multishippingCheckout->getQuote();
        return $this->orderApprovalManagement->isApproveRequiredForCart($quote->getId());
    }
}
