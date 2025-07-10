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
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;

/**
 * Class IsProductSalableForRequestedQtyFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi
 */
class IsProductSalableForRequestedQtyFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Get instance
     *
     * @return IsProductSalableForRequestedQtyInterface
     */
    public function create()
    {
        return $this->objectManager->get(IsProductSalableForRequestedQtyInterface::class);
    }
}
