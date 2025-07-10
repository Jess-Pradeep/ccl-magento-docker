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
namespace Aheadworks\Ca\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Aheadworks\Ca\Model\ResourceModel\Role;

class AssignIsDefaultFlagToRootRole implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Assign is default flag to root role
     */
    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();

        $select = $connection->select()->from(
            $this->moduleDataSetup->getTable(CompanyUser::MAIN_TABLE_NAME),
            [
                CompanyUserInterface::COMPANY_ROLE_ID
            ]
        )->where(CompanyUserInterface::IS_ROOT . ' = 1');
        $rootRoleIds = $connection->fetchCol($select);

        if (count($rootRoleIds)) {
            $connection->update(
                $this->moduleDataSetup->getTable(Role::MAIN_TABLE_NAME),
                [
                    RoleInterface::IS_DEFAULT => 1
                ],
                RoleInterface::ID . ' in (' . implode(',', array_values($rootRoleIds)) . ')'
            );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.1.0';
    }
}
