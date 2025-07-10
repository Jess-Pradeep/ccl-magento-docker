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
namespace Aheadworks\Ca\Plugin\Model\Sales;

use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;
use Magento\Sales\Model\Order;

/**
 * Class OrderPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\Sales
 */
class OrderPlugin
{
    /**
     * @var IsActiveChecker
     */
    private $isActiveChecker;

    /**
     * @param IsActiveChecker $isActiveChecker
     */
    public function __construct(
        IsActiveChecker $isActiveChecker
    ) {
        $this->isActiveChecker = $isActiveChecker;
    }

    /**
     * Check if order can be put on hold
     *
     * @param Order $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanHold(Order $subject, $result)
    {
        return $this->canProceed($subject) ? $result : false;
    }

    /**
     * Check if order can be released from on hold status
     *
     * @param Order $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanUnhold(Order $subject, $result)
    {
        return $this->canProceed($subject) ? $result : false;
    }

    /**
     * Check if order can be processed
     *
     * @param Order $order
     * @return bool
     */
    private function canProceed($order)
    {
        return !($this->isActiveChecker->isOrderUnderApprovalConsideration($order)
            || $this->isActiveChecker->isOrderRejected($order));
    }
}
