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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Api;

/**
 * Interface SummaryRepositoryInterface
 * @api
 */
interface SummaryRepositoryInterface
{
    /**
     * Constant to use for fast getting list items
     */
    public const COLLECTION_PAGE_DEFAULT_SIZE = 20000;

    /**
     * Save credit limit summary
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary);

    /**
     * Retrieve credit limit summary by customer ID
     *
     * @param int $customerId
     * @param bool $reload
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $reload = false);

    /**
     * Retrieve credit limit summary items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
