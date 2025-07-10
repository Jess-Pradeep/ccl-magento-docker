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

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface IsNotSalableForRequestedQtyResultInterface
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
interface IsNotSalableForRequestedQtyResultInterface
{
    /**
     * Get result message in case product is not salable for requested qty
     *
     * @param ProductInterface $product
     * @param int|float $requestedQty
     * @return string
     */
    public function getResultMessage($product, $requestedQty);
}
