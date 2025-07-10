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
namespace Aheadworks\Ca\Model\Company\Domain\Search;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Ca\Model\Company\Domain\Search
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CompanyDomainRepositoryInterface
     */
    private $companyDomainRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CompanyDomainRepositoryInterface $companyDomainRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CompanyDomainRepositoryInterface $companyDomainRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyDomainRepository = $companyDomainRepository;
    }

    /**
     * Search domains according to prepared search criteria
     *
     * @return CompanyDomainInterface[]
     * @throws LocalizedException
     */
    public function searchDomains()
    {
        $searchResults = $this->companyDomainRepository
            ->getList($this->buildSearchCriteria());

        return $searchResults->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add name filter
     *
     * @param string $domainName
     * @return $this
     */
    public function addNameFilter($domainName)
    {
        $this->getSearchCriteriaBuilder()->addFilter(CompanyDomainInterface::NAME, $domainName);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param string $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSearchCriteriaBuilder()->addFilter(CompanyDomainInterface::STATUS, $status);
        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
