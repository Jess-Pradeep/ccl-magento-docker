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

/**
 * Interface StateInterface
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
interface StateInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const INITIAL_ORDER_STATUS = 'initial_order_status';
    const INITIAL_ORDER_STATE = 'initial_order_state';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get order ID
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get initial order status
     *
     * @return string
     */
    public function getInitialOrderStatus();

    /**
     * Set initial order status
     *
     * @param string $status
     * @return $this
     */
    public function setInitialOrderStatus($status);

    /**
     * Get initial order state
     *
     * @return string
     */
    public function getInitialOrderState();

    /**
     * Set initial order state
     *
     * @param string $state
     * @return $this
     */
    public function setInitialOrderState($state);
}
