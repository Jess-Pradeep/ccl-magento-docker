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
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item;

use Aheadworks\RequisitionLists\Model\RequisitionList\Item as ItemModel;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Serializer as OptionSerializer;

/**
 * Class ObjectDataProcessor
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item
 */
class ObjectDataProcessor
{
    /**
     * @var OptionSerializer
     */
    private $optionSerializer;

    /**
     * @param OptionSerializer $optionSerializer
     */
    public function __construct(
        OptionSerializer $optionSerializer
    ) {
        $this->optionSerializer = $optionSerializer;
    }

    /**
     * Prepare entity data before save
     *
     * @param ItemModel $item
     * @return ItemModel
     */
    public function prepareDataBeforeSave($item)
    {
        $item->setProductOption($this->optionSerializer->serializeToString($item->getProductOption()));
        return $item;
    }

    /**
     * Prepare entity data after load
     *
     * @param ItemModel $item
     * @return ItemModel
     */
    public function prepareDataAfterLoad($item)
    {
        $item->setProductOption($this->optionSerializer->unserializeToObject($item->getProductOption()));
        return $item;
    }
}
