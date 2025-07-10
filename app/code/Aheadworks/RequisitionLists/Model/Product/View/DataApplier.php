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
namespace Aheadworks\RequisitionLists\Model\Product\View;

use Magento\Framework\Registry;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Product\BuyRequest\Processor;

/**
 * Class DataApplier
 * @package Aheadworks\RequisitionLists\Model\Product\View
 */
class DataApplier
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Processor
     */
    private $buyRequestProcessor;

    /**
     * @param Registry $coreRegistry
     * @param Processor $buyRequestProcessor
     */
    public function __construct(
        Registry $coreRegistry,
        Processor $buyRequestProcessor
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->buyRequestProcessor = $buyRequestProcessor;
    }

    /**
     * Apply product data required for product view rendering
     *
     * @param ProductInterface|Product $product
     * @param RequisitionListItemInterface $item
     */
    public function apply($product, $item)
    {
        $this->coreRegistry->register('product', $product);
        $this->coreRegistry->register('current_product', $product);

        $buyRequest = $this->buyRequestProcessor->prepareBuyRequest($item);
        $optionValues = $product->processBuyRequest($buyRequest);
        $product->setPreconfiguredValues($optionValues);
        $product->setConfigureMode(true);
    }
}
