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
namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Role
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Role extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_ca_role';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RoleInterface::ID);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|RoleInterface $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->isDefault()) {
            $this->getConnection()
                ->update(
                    $this->getMainTable(),
                    [RoleInterface::IS_DEFAULT => 0],
                    RoleInterface::COMPANY_ID . ' = ' . $object->getCompanyId()
                    . ' And '
                    . RoleInterface::ID . ' != ' . $object->getId()
                );
        }
        return parent::_afterSave($object);
    }

    /**
     * Get role ids by company ID
     *
     * @param int $companyId
     * @return array
     * @throws LocalizedException
     */
    public function getRoleIdsByCompanyId(int $companyId): array
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getMainTable(),
                [RoleInterface::ID]
            )->where(RoleInterface::COMPANY_ID . ' = (?)', $companyId);

        return $this->getConnection()->fetchCol($select);
    }
}
