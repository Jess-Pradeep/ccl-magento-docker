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
namespace Aheadworks\QuickOrder\Model\ProductList\Item\Processor;

use Aheadworks\QuickOrder\Model\ProductList\Item\ProcessorInterface;

/**
 * Class Common
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item\Processor
 */
class Common implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process($requestItem, $item, $product)
    {
        if (!$item->getItemKey()) {
            $item->setItemKey(uniqid('', false));
        }
        $item->setProductId($product->getId());
        $item->setProductName($product->getName());
        $item->setProductSku($product->getSku());
        $item->setProductType($product->getTypeId());
    }
}
