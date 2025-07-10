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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate\Grid;

use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface as FrameworkSearchCriteriaInterface;
use Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate as CandidateResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate\Collection as CandidateCollection;

class Collection extends CandidateCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected AggregationInterface $aggregations;

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Document::class, CandidateResourceModel::class);
    }

    /**
     * Init select
     *
     * @return $this
     */
    protected function _initSelect(): self
    {
        parent::_initSelect();
        $this->getSelect()->joinInner(
            ['customer_table' => $this->getTable('customer_entity')],
            'customer_table.entity_id = main_table.' . CompanyAdminCandidateInterface::CUSTOMER_ID,
            [
                'customer_name' => 'CONCAT(customer_table.firstname, " ", customer_table.lastname)',
                'customer_email' => 'customer_table.email'
            ]
        );

        return $this;
    }

    /**
     * Get aggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations(): AggregationInterface
    {
        return $this->aggregations;
    }

    /**
     * Set aggregations
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations): self
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return FrameworkSearchCriteriaInterface|null
     */
    public function getSearchCriteria(): ?FrameworkSearchCriteriaInterface
    {
        return null;
    }

    /**
     * Set search criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(?SearchCriteriaInterface $searchCriteria = null): self
    {
        return $this;
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount): self
    {
        return $this;
    }

    /**
     * Set items list
     *
     * @param DocumentInterface[] $items
     * @return $this
     */
    public function setItems(?array $items = null): self
    {
        return $this;
    }
}
