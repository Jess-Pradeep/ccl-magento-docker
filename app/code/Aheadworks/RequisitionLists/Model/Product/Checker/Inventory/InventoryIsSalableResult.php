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

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;

/**
 * Class InventoryIsSalableResult
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
class InventoryIsSalableResult implements IsProductSalableResultInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var StockStateProviderInterface
     */
    private $stockStateProvider;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateProviderInterface $stockStateProvider
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        StockStateProviderInterface $stockStateProvider
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->stockStateProvider = $stockStateProvider;
    }

    /**
     * @inheritdoc
     */
    public function isSalable($product, $requestedQty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $isSalableResult = $this->stockStateProvider->checkQuoteItemQty($stockItem, $requestedQty, $requestedQty);

        return !$isSalableResult->getHasError();
    }
}
