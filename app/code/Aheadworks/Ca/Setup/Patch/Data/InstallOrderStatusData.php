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

use Exception;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Aheadworks\Ca\Setup\Updater\Data\OrderStatus as OrderStatusData;

class InstallOrderStatusData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var OrderStatusData
     */
    private $orderStatusData;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param OrderStatusData $orderStatusData
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        OrderStatusData $orderStatusData,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->orderStatusData = $orderStatusData;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Install order status data
     *
     * @throws Exception
     */
    public function apply(): self
    {
        $this->orderStatusData->install($this->moduleDataSetup);

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
        return '1.6.0';
    }
}
