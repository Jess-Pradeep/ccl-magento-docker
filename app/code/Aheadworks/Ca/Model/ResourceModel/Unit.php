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

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Ca\Api\Data\UnitInterface;

class Unit extends AbstractDb
{
    /**
     * Unit Table
     */
    public const CA_UNIT_TABLE = 'aw_ca_unit';

    /**
     * Init Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::CA_UNIT_TABLE, 'id');
    }

    /**
     * Unset path before save
     * 
     * @param UnitInterface $object
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->unsetData(UnitInterface::PATH);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Set path after save
     * 
     * @param UnitInterface $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (empty($object->getPath()) && !$object->getParentId()) {
            $this->getConnection()->update(
                $this->getTable(self::CA_UNIT_TABLE),
                [UnitInterface::PATH => $object->getId()],
                [UnitInterface::UNIT_ID . ' = ?' => $object->getId()]
            );
        } elseif (empty($object->getPath()) && $object->getParentId()) {
            $this->updatePathByParent($object);
        }
        return parent::_afterSave($object);
    }

    /**
     * Update path by parent unit
     *
     * @param UnitInterface $unit
     */
    private function updatePathByParent($unit)
    {
        $select = $this->getConnection()->select()
            ->from([$this->getTable(self::CA_UNIT_TABLE)], [UnitInterface::PATH])
            ->where(UnitInterface::UNIT_ID . ' = ?', $unit->getParentId());

        $parentUnitPath = $select->getConnection()->fetchOne($select);

        $this->getConnection()->update(
            $this->getTable(self::CA_UNIT_TABLE),
            [UnitInterface::PATH => $parentUnitPath . '/' . $unit->getId()],
            [UnitInterface::UNIT_ID . ' = ?' => $unit->getId()]
        );
    }

    /**
     * Return all child units
     *
     * @param int $unitId
     * @return array
     */
    public function getAllChildUnits($unitId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::CA_UNIT_TABLE))
            ->where(UnitInterface::PATH . ' LIKE ?', $unitId . '/%')
            ->orWhere(UnitInterface::PATH . ' LIKE ?', '%/' . $unitId . '/%')
            ->orWhere(UnitInterface::PATH . ' LIKE ?', '%/' . $unitId);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Retrieve root unit
     *
     * @param int $companyId
     * @return int
     * @throws LocalizedException
     */
    public function getCompanyRootUnit($companyId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('`company_id` = :company_id')
            ->where('`parent_id` = :parent_id');

        return $connection->fetchOne($select, ['company_id' => $companyId, 'parent_id' => 0]);
    }
}
