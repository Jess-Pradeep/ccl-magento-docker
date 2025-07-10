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
namespace Aheadworks\Ctq\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class Quote extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ctq_quote';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, QuoteInterface::ID);
    }

    /**
     * Get quote identifier by cart id
     *
     * @param int $cartId
     * @return int|false
     * @throws LocalizedException
     */
    public function getIdByCartId($cartId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('cart_id = :cart_id');

        return $connection->fetchOne($select, ['cart_id' => $cartId]);
    }

    /**
     * Get quote identifier by order id
     *
     * @param int $orderId
     * @return int|false
     * @throws LocalizedException
     */
    public function getIdByOrderId($orderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('order_id = :order_id');

        return $connection->fetchOne($select, ['order_id' => $orderId]);
    }

    /**
     * Get quote identifier by hash
     *
     * @param string $hash
     * @return int|false
     * @throws LocalizedException
     */
    public function getIdByHash($hash)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('hash = :hash');

        return $connection->fetchOne($select, ['hash' => $hash]);
    }

    /**
     * Set customer ID to all quotes with specified email
     *
     * This is used when new customer registers
     *
     * @param int $customerIdToSet
     * @param string $questEmail
     * @return bool
     * @throws LocalizedException
     */
    public function setCustomerIdToGuestQuotes($customerIdToSet, $questEmail)
    {
        $connection = $this->getConnection();
        $data = [
            QuoteInterface::CUSTOMER_ID => $customerIdToSet
        ];
        $condition = [
            $connection->quoteInto(QuoteInterface::CUSTOMER_EMAIL . ' = ?', $questEmail),
            QuoteInterface::CUSTOMER_ID . ' IS NULL'
        ];
        $rowsCount = $connection->update(
            $this->getTable($this->getMainTable()),
            $data,
            implode(' AND ', $condition)
        );

        return $rowsCount > 0;
    }
}
