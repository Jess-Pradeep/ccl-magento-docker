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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\Product\Search;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory;
use Magento\Framework\Api\SortOrderBuilder;

class SearchCriteriaBuilder
{
    /**
     * @var SearchCriteriaInterface
     */
    private SearchCriteriaInterface $searchCriteria;

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var array
     */
    private array $sortOrders = [];

    /**
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaInterfaceFactory $searchCriteriaFactory
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        private FilterBuilder $filterBuilder,
        private FilterGroupBuilder $filterGroupBuilder,
        private SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        private SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchCriteria = $this->searchCriteriaFactory->create();
    }

    /**
     * Build Search Criteria
     *
     * @return SearchCriteriaInterface
     */
    public function build(): SearchCriteriaInterface
    {
        $searchCriteria = $this->searchCriteria;
        if ($this->filters) {
            $filterGroups = [];
            foreach ($this->filters as $group => $filters) {
                $filterGroups[$group] = $this->filterGroupBuilder->create()->setFilters($filters);
            }

            $searchCriteria
                ->setSortOrders($this->sortOrders)
                ->setFilterGroups($filterGroups);
        }
        $this->reset();

        return $searchCriteria;
    }

    /**
     * Add Filter
     *
     * @param string $field
     * @param mixed $value
     * @param string $conditionType
     * @param string $group
     * @return $this
     */
    public function addFilter(string $field, $value, string $conditionType = 'eq', string $group = 'default'): self
    {
        if (!isset($this->filters[$group])) {
            $this->filters[$group] = [];
        }

        $this->filters[$group][] = $this->filterBuilder
            ->setField($field)
            ->setValue($value)
            ->setConditionType($conditionType)
            ->create();

        return $this;
    }

    /**
     * Set Request Name
     *
     * @param string $requestName
     * @return self
     */
    public function setRequestName(string $requestName): self
    {
        $this->searchCriteria->setRequestName($requestName);

        return $this;
    }

    /**
     * Set Page Size
     *
     * @param int $pageSize
     * @return self
     */
    public function setPageSize(int $pageSize): self
    {
        $this->searchCriteria->setPageSize($pageSize);

        return $this;
    }

    /**
     * Add Sort Order
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addSortOrder(string $field, string $direction): self
    {
        $this->sortOrders[] = $this->sortOrderBuilder
            ->setField($field)
            ->setDirection($direction)
            ->create();

        return $this;
    }

    /**
     * Reset Params
     *
     * @return void
     */
    private function reset(): void
    {
        $this->searchCriteria = $this->searchCriteriaFactory->create();
        $this->sortOrders = [];
        $this->filters = [];
    }
}
