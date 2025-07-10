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
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\Url;

/**
 * Class MoveTo
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option
 */
class MoveTo extends AbstractOption
{
    /**
     * {@inheritDoc}
     */
    protected function prepareUrl($list)
    {
        return $this->urlBuilder->getUrl(
            Url::REQUISITION_LIST_ROUTE . '/moveItem',
            [
                RequisitionListInterface::LIST_ID => $this->listProvider->getRequisitionListId(),
                'move_to_list' => $list->getListId()
            ]
        );
    }
}
