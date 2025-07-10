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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Model\Product\Checker\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface IsProductSalableResultInterface
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
interface IsProductSalableResultInterface
{
    /**
     * Check if product is salable for requested qty
     *
     * @param ProductInterface $product
     * @param int|float $requestedQty
     * @return bool
     */
    public function isSalable($product, $requestedQty);
}
