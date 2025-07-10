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
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\RequisitionLists\Model\Config;

/**
 * Class Checker
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
class Checker
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var IsSalableResultFactory
     */
    private $isSalableResultFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param IsSalableResultFactory $isSalableResultFactory
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        IsSalableResultFactory $isSalableResultFactory,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->isSalableResultFactory = $isSalableResultFactory;
        $this->config = $config;
    }

    /**
     * Retrieve if product has stock or config is set for showing out of stock products
     *
     * @param Product|ProductInterface $product
     * @param null|int $qty
     * @return bool
     */
    public function isProductVisible($product, $qty = null)
    {
        return !$product->isDisabled()
            && ($this->isProductInStock($product, $qty) || $this->config->isConfiguredToShowOutOfStockProducts());
    }

    /**
     * Retrieve if parent product is salable or config is set for showing out of stock products
     *
     * @param Product|ProductInterface $product
     * @return bool
     */
    public function isParentProductVisible($product)
    {
        return !$product->isDisabled()
            && ($product->isSalable() || $this->config->isConfiguredToShowOutOfStockProducts());
    }

    /**
     * Get salable result for product
     *
     * @param Product|ProductInterface $product
     * @param float|int|null $requestedQty
     * @return bool
     */
    public function isProductInStock($product, $requestedQty = null)
    {
        if ($requestedQty === null) {
            $requestedQty = $this->config->getMinSalableQty();
        }

        $isSalableResult = $this->isSalableResultFactory->create();
        return $isSalableResult->isSalable($product, $requestedQty);
    }
}
