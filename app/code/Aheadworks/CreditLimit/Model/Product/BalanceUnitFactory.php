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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BalanceUnitFactory
 *
 * @package Aheadworks\Ca\Model\Config\Backend
 */
class BalanceUnitFactory
{
    /**
     * @var ProductInterfaceFactory
     */
    private $productFactory;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ProductInterfaceFactory $productFactory
     * @param ProductResource $productResource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductInterfaceFactory $productFactory,
        ProductResource $productResource,
        StoreManagerInterface $storeManager
    ) {
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
    }

    /**
     * Create balance unit product instance
     *
     * @return ProductInterface
     * @throws LocalizedException
     */
    public function create()
    {
        /** @var ProductInterface|Product $product */
        $product = $this->productFactory->create();
        $product
            ->setSku(BalanceUnitInterface::SKU)
            ->setName(BalanceUnitInterface::PRODUCT_NAME)
            ->setPrice(BalanceUnitInterface::PRODUCT_PRICE)
            ->setStatus(Status::STATUS_ENABLED)
            ->setTypeId(ProductType::TYPE_VIRTUAL)
            ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
            ->setAttributeSetId($this->productResource->getEntityType()->getDefaultAttributeSetId())
            ->setWebsiteIds($this->prepareWebsiteIdsToAssign())
            ->setCustomAttribute('tax_class_id', 0)
            ->setStockData(
                [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                    'is_in_stock' => 1
                ]
            );

        return $product;
    }

    /**
     * Prepare website IDs balance unit is assigned to
     *
     * @return array
     */
    private function prepareWebsiteIdsToAssign()
    {
        $websiteIds = [];
        $websites = $this->storeManager->getWebsites();
        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }
}
