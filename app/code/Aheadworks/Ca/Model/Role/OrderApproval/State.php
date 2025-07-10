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

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Model\ResourceModel\Role\OrderApproval\State as StateResourceModel;

/**
 * Class State
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
class State extends AbstractModel implements StateInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(StateResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getInitialOrderStatus()
    {
        return $this->getData(self::INITIAL_ORDER_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setInitialOrderStatus($status)
    {
        return $this->setData(self::INITIAL_ORDER_STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getInitialOrderState()
    {
        return $this->getData(self::INITIAL_ORDER_STATE);
    }

    /**
     * @inheritdoc
     */
    public function setInitialOrderState($state)
    {
        return $this->setData(self::INITIAL_ORDER_STATE, $state);
    }
}
