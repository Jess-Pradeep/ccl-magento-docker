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
namespace Aheadworks\RequisitionLists\Model\Product\Checker;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @package Aheadworks\RequisitionLists\Model\Product\Checker
 */
class ProhibitedTypeChecker
{
    /**
     * @var array
     */
    private $prohibitedTypes = ['giftcard', 'aw_giftcard', 'aw_event_ticket'];

    /**
     * @param array $prohibitedTypes
     */
    public function __construct(
        $prohibitedTypes = []
    ) {
        $this->prohibitedTypes = array_merge($this->prohibitedTypes, $prohibitedTypes);
    }

    /**
     * Check is product prohibited
     *
     * @param ProductInterface|ExtensibleDataInterface $item
     * @return bool
     */
    public function isProductProhibited($item) {
        $productType = $item->getTypeId() ?: $item->getProductType();

        return in_array($productType, $this->prohibitedTypes);
    }
}