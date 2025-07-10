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
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface RequisitionListItemRepositoryInterface
 * @api
 */
interface RequisitionListItemRepositoryInterface
{
    /**
     * Retrieve list item by its ID
     *
     * @param int $listItemId
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($listItemId);

    /**
     * Retrieve list items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Save list item
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem);

    /**
     * Delete list item
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem);


    /**
     * Delete list item by ID
     *
     * @param int $listItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($listItemId);
}
