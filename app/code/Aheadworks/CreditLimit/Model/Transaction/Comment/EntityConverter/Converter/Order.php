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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter;

use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\ConverterInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\EntityType;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\DataObject;

/**
 * Class Order
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter
 */
class Order extends AbstractConverter implements ConverterInterface
{
    /**
     * Convert object to transaction entity
     *
     * @param DataObject|OrderInterface $order
     * @return TransactionEntityInterface
     */
    public function convertToTransactionEntity($order)
    {
        /** @var TransactionEntityInterface $transactionEntity */
        $transactionEntity = $this->transactionEntityFactory->create();
        $transactionEntity
            ->setEntityId($order->getIncrementId())
            ->setEntityLabel($order->getIncrementId())
            ->setEntityType(EntityType::ORDER_ID);

        return $transactionEntity;
    }
}
