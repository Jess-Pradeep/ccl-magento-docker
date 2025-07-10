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
 * Class RequisitionListSearchResultsInterface
 * @api
 */
interface RequisitionListSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get product list items
     *
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface[]
     */
    public function getItems();

    /**
     * Set product list items
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
