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
namespace Aheadworks\RequisitionLists\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class RequisitionListItemSearchResultsInterface
 * @api
 */
interface RequisitionListItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list items
     *
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface[]
     */
    public function getItems();

    /**
     * Set list items
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
