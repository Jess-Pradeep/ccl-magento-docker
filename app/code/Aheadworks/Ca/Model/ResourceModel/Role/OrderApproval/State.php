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
namespace Aheadworks\Ca\Model\ResourceModel\Role\OrderApproval;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Ca\Model\Role\OrderApproval\StateInterface;

/**
 * Class State
 *
 * @package Aheadworks\Ca\Model\ResourceModel\Role\OrderApproval
 */
class State extends AbstractDb
{
    const MAIN_TABLE_NAME = 'aw_ca_order_approval_state';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, StateInterface::ID);
    }

    /**
     * Get state by order ID
     *
     * @param string $orderId
     * @return int|false
     * @throws LocalizedException
     */
    public function getStateIdByOrderId($orderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('order_id = :order_id');

        return $connection->fetchOne($select, ['order_id' => $orderId]);
    }
}
