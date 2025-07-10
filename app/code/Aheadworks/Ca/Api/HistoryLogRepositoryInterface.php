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
namespace Aheadworks\Ca\Api;

/**
 * Interface HistoryLogRepositoryInterface
 * @api
 */
interface HistoryLogRepositoryInterface
{
    /**
     * Save History Log
     *
     * @param \Aheadworks\Ca\Api\Data\HistoryLogInterface $historyLog
     * @return \Aheadworks\Ca\Api\Data\HistoryLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ca\Api\Data\HistoryLogInterface $historyLog);

    /**
     * Retrieve History Log by id
     *
     * @param int $historyLogId
     * @return \Aheadworks\Ca\Api\Data\HistoryLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($historyLogId);

    /**
     * Retrieve History Log list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete History Log
     *
     * @param \Aheadworks\Ca\Api\Data\HistoryLogInterface $historyLogRecord
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Ca\Api\Data\HistoryLogInterface $historyLogRecord);

    /**
     * Delete History Log by ID
     *
     * @param int $historyLogId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($historyLogId);
}
