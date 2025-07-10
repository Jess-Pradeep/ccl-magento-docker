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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\CreditSummary\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;

class Builder
{
    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(
        private readonly SearchCriteriaBuilder  $searchCriteriaBuilder,
        private readonly SummaryRepositoryInterface $summaryRepository
    ) {
    }

    /**
     * Get summaries
     *
     * @return SummaryInterface[]
     * @throws LocalizedException
     */
    public function searchSummaries(): array
    {
        return $this->summaryRepository
            ->getList($this->buildSearchCriteria())
            ->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add websites filter
     *
     * @param array $websiteIds
     * @return $this
     */
    public function addWebsitesFilter(array $websiteIds): self
    {
        $this->searchCriteriaBuilder->addFilter(
            SummaryInterface::WEBSITE_ID,
            $websiteIds,
            'in'
        );

        return $this;
    }

    /**
     * Add negative balance date filter
     *
     * @return $this
     */
    public function addNegativeBalanceDateFilter(): self
    {
        $this->searchCriteriaBuilder->addFilter(
            SummaryInterface::NEGATIVE_BALANCE_DATE,
            null,
            'notnull'
        );

        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteriaBuilder->create();
    }
}
