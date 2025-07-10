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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Company\Admin\Candidate\Search;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyAdminCandidateRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;

class Builder
{
    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CompanyAdminCandidateRepositoryInterface $candidateRepository
     */
    public function __construct(
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CompanyAdminCandidateRepositoryInterface $candidateRepository
    ) {
    }

    /**
     * Search company admin candidates according to prepared search criteria
     *
     * @return CompanyAdminCandidateInterface[]
     * @throws LocalizedException
     */
    public function searchCandidates(): array
    {
        $searchResults = $this->candidateRepository
            ->getList($this->buildSearchCriteria());

        return $searchResults->getItems();
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
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter(int $customerId): self
    {
        $this->getSearchCriteriaBuilder()->addFilter(CompanyAdminCandidateInterface::CUSTOMER_ID, $customerId);
        return $this;
    }

    /**
     * Add company filter
     *
     * @param int $companyId
     * @return $this
     */
    public function addCompanyFilter(int $companyId): self
    {
        $this->getSearchCriteriaBuilder()->addFilter(CompanyAdminCandidateInterface::COMPANY_ID, $companyId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param string $status
     * @return $this
     */
    public function addStatusFilter(string $status): self
    {
        $this->getSearchCriteriaBuilder()->addFilter(CompanyAdminCandidateInterface::STATUS, $status);
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
