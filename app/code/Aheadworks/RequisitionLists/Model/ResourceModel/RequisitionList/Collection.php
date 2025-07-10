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
namespace Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList;

use Aheadworks\RequisitionLists\Model\ResourceModel\AbstractCollection;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList as RequisitionListResource;

/**
 * Class Collection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = RequisitionListInterface::LIST_ID;

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'aw_rl_collection';

    /**
     * {@inheritdoc}
     */
    protected $_eventObject = 'aw_rl_collection_object';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RequisitionList::class, RequisitionListResource::class);
    }
}
