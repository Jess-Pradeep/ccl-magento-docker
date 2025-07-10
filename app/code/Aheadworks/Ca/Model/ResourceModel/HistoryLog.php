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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class HistoryLog
 */
class HistoryLog extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_ca_history_log';
    /**#@-*/

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        DateTime $dateTime,
        $connectionName = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($context, $entityManager, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE_NAME, HistoryLogInterface::ID);
    }

    /**
     * Clean The History Log
     *
     * @param int $lifetime
     * @return void
     */
    public function cleanLog(int $lifetime): void
    {
        $connection = $this->getConnection();
        $clearBefore = $this->dateTime->formatDate(time() - $lifetime);
        $select = $connection->select()
            ->from($this->getMainTable(),HistoryLogInterface::ID)
            ->where('time < ?', $clearBefore)
            ->order(HistoryLogInterface::ID . ' DESC')
            ->limit(1);

        $latestLogEntry = $connection->fetchOne($select);
        if ($latestLogEntry) {
            $connection->delete($this->getMainTable(), [HistoryLogInterface::ID . ' <= ?' => $latestLogEntry]);
        }
    }

    /**
     * Get last history log record by entity ID
     *
     * @param int $entityId
     * @param string $entityType
     * @return array
     */
    public function getLastLogRecordByEntityId(int $entityId, string $entityType): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(),[
                HistoryLogInterface::ID,
                HistoryLogInterface::CUSTOMER_NAME,
                HistoryLogInterface::ENTITY_ID,
                HistoryLogInterface::ENTITY_TYPE
            ])
            ->where(HistoryLogInterface::ENTITY_ID . ' = ?', $entityId)
            ->where(HistoryLogInterface::ENTITY_TYPE . ' = ?', $entityType)
            ->order(HistoryLogInterface::ID . ' DESC')
            ->limit(1);

        return $connection->fetchRow($select) ?: [];
    }
}
