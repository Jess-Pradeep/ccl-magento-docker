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
namespace Aheadworks\Ca\Plugin\Helper;

use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Reorder;

/**
 * Class ReorderPlugin
 *
 * @package Aheadworks\Ca\Plugin\Helper
 */
class ReorderPlugin
{
    /**
     * @var IsActiveChecker
     */
    private $isActiveChecker;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param IsActiveChecker $isActiveChecker
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        IsActiveChecker $isActiveChecker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->isActiveChecker = $isActiveChecker;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Prevent order from reordering if it was rejected
     *
     * @param Reorder $subject
     * @param bool $result
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanReorder($subject, $result, $orderId)
    {
        $order = $this->orderRepository->get($orderId);
        if ($this->isActiveChecker->isOrderRejected($order)) {
            $result = false;
        }

        return $result;
    }
}
