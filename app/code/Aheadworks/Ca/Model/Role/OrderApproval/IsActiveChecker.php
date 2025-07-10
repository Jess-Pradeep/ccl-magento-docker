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
namespace Aheadworks\Ca\Model\Role\OrderApproval;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\OrderStatus;

/**
 * Class IsActiveChecker
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
class IsActiveChecker
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Check if order must be approved for quote for provided role
     *
     * @param int $roleId
     * @param Quote $quote
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkForQuote($roleId, $quote)
    {
        $role = $this->roleRepository->get($roleId);
        return $this->validateLimit(
            $role->getOrderBaseAmountLimit(),
            $quote->getBaseSubtotalWithDiscount()
        );
    }

    /**
     * Check if order must be approved for quote for provided role
     *
     * @param int $roleId
     * @param OrderInterface|Order $order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkForOrder($roleId, $order)
    {
        $role = $this->roleRepository->get($roleId);
        return $this->validateLimit(
            $role->getOrderBaseAmountLimit(),
            $order->getBaseSubtotal() - abs($order->getBaseDiscountAmount() ?? 0)
        );
    }

    /**
     * Check if order under approval consideration
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function isOrderUnderApprovalConsideration($order)
    {
        return $order->getStatus() == OrderStatus::PENDING_APPROVAL;
    }

    /**
     * Check if order was rejected
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function isOrderRejected($order)
    {
        return $order->getStatus() == OrderStatus::REJECTED;
    }

    /**
     * Validate limit
     *
     * @param float|null $limit
     * @param float $total
     * @return bool
     */
    private function validateLimit($limit, $total)
    {
        if (empty($limit) && !is_numeric($limit)) {
            return false;
        }

        if ($limit == 0 || $total >= $limit) {
            return true;
        }

        return false;
    }
}
