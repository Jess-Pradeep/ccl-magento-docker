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
namespace Aheadworks\Ca\Ui\DataProvider\Order\Listing\Modifier;

use Magento\Sales\Model\Order;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * Class OrderTotal
 * @package Aheadworks\Ca\Ui\DataProvider\Order\Listing\Modifier
 */
class OrderTotal implements ModifierInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $items)
    {
        foreach ($items as &$item) {
            /** @var Order $order */
            $order = $this->orderRepository->get($item[Order::ENTITY_ID]);
            if ($order) {
                $item[Order::GRAND_TOTAL] = $order->getOrderCurrency()
                    ->formatPrecision($item[Order::GRAND_TOTAL], 2, [], false);
            }
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
