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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Source\Order;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;

/**
 * Class Status
 * @package Aheadworks\Ca\Model\Source\Order
 */
class Status implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collection = $collectionFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->collection
            ->joinStates()
            ->addFieldToFilter('visible_on_front', ['eq' => true])
            ->toOptionArray();
    }
}
