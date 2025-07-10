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
namespace Aheadworks\RequisitionLists\Model\ResourceModel\Sales\Order\Item;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;

/**
 * Class Collection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\Sales\Order\Item
 */
class Collection extends OrderItemCollection
{
    /**
     * {@inheritDoc}
     */
    protected $_idFieldName = 'item_id';
}
