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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\QuickOrder\Model\ThirdPartyModule\Manager;

/**
 * Class IsNotSalableResultFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
class IsNotSalableResultFactory
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Manager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Manager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Create is product salable result
     *
     * @return IsNotSalableForRequestedQtyResultInterface
     */
    public function create()
    {
        return $this->moduleManager->isMagentoMsiModuleEnabled()
            ? $this->objectManager->get(MagentoMsiIsNotSalableResult::class)
            : $this->objectManager->get(InventoryIsNotSalableResult::class);
    }
}
