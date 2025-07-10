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
 * Interface RequisitionListRepositoryInterface
 * @api
 */
interface RequisitionListRepositoryInterface
{
    /**
     * Retrieve list by its ID
     *
     * @param int $listId
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($listId);

    /**
     * Save list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list);

    /**
     * Retrieve list items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list);

    /**
     * Delete list by ID
     *
     * @param int $listId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($listId);
}
