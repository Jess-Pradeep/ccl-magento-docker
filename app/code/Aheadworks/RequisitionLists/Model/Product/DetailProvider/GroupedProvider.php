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
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

/**
 * Class GroupedProvider
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
class GroupedProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    public function isAvailableForSite()
    {
        return $this->isProductInWebsite($this->product);
    }

    /**
     * @inheritDoc
     */
    public function isDisabled()
    {
        return $this->product->isDisabled();
    }

    /**
     * @inheritDoc
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isSalable()
    {
        return $this->product->isSalable();
    }

    /**
     * @inheritDoc
     */
    public function isQtyEnabled()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function resolveSubProducts($products)
    {
        $this->subProducts = $products;
    }

    /**
     * @inheritdoc
     */
    protected function getProductTypeAttributes($productOption)
    {
        $selectedProducts = [];
        foreach ($this->subProducts as $product) {
            $selectedProducts[] = [
                'name' => $product->getName(),
                'qty' => $product->getCartQty()
            ];
        }

        return $selectedProducts;
    }
}
