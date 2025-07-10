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
namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Transaction
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class Transaction extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_transaction';

    /**
     * Transaction entity table
     */
    const TRANSACTION_ENTITY_TABLE = 'aw_cl_transaction_entity';

    /**
     * Constant to save multiple records count at once
     */
    public const MULTIPLE_SAVE_RECORDS_COUNT_AT_ONCE = 1000;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TransactionInterface::ID);
    }

    /**
     * Save multiple objects
     *
     * @param AbstractModel[] $objects
     * @return void
     * @throws \Exception
     */
    public function saveMultiple(array $objects): void
    {
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();
            $data = [];
            foreach ($objects as $object) {
                $data[] = $this->_prepareDataForSave($object);
            }
            foreach (array_chunk($data, self::MULTIPLE_SAVE_RECORDS_COUNT_AT_ONCE) as $saveData) {
                $connection->insertOnDuplicate(
                    $connection->getTableName(self::MAIN_TABLE_NAME),
                    $saveData
                );
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
