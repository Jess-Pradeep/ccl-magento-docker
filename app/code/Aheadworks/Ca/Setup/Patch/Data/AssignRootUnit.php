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

namespace Aheadworks\Ca\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResourceModel;
use Aheadworks\Ca\Api\UnitManagementInterface;

class AssignRootUnit implements DataPatchInterface
{
    /**
     * AssignRootUnit Construct
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param UnitManagementInterface $unitManagment
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly UnitManagementInterface $unitManagment
    ) {
    }

    /**
     * Assign is default flag to root role
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()
            ->from($this->moduleDataSetup->getTable(CompanyResourceModel::MAIN_TABLE_NAME));
        $companies = $connection->fetchAll($select);
        foreach ($companies as $company) {
            $this->unitManagment->createDefaultUnit((int)$company["id"]);
        }
        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * Ge aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
