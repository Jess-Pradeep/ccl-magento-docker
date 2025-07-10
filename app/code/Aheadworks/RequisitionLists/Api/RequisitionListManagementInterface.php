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
 * Interface RequisitionListManagementInterface
 * @api
 */
interface RequisitionListManagementInterface
{
    /**
     * Add item to list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $item
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function addItem(\Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $item): \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;

    /**
     * Update item option
     *
     * @param int $itemId
     * @param array $buyRequest
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateItemOption(int $itemId, array $buyRequest): \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;

    /**
     * Move item to list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $item
     * @param int $listIdToMove
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function moveItem($item, $listIdToMove);
}
