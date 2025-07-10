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

namespace Aheadworks\Ca\Model\ResourceModel\Company\User\Grid;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\ResourceModel\Company\User\Collection as CompanyUserCollection;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface as FrameworkSearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

class Collection extends CompanyUserCollection implements SearchResultInterface
{
    /**
     * Fields map for correlation names & real selected fields
     *
     * @var array
     */
    protected $_map = [
        'fields' => [
            'company_id' => 'main_table.company_id',
            'customer_email' => 'customer_table.email',
            'customer_name' => 'customer_table.firstname'
        ]
    ];

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Document::class, CompanyUserResourceModel::class);
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
            'customer_table.entity_id = main_table.' . CompanyUserInterface::CUSTOMER_ID,
            [
                'customer_name' => 'CONCAT(customer_table.firstname, " ", customer_table.lastname)',
                'customer_email' => 'customer_table.email',
                'entity_id' => 'customer_table.entity_id',
            ]
        );
        $this->getSelect()->joinLeft(
            ['aw_role_table' => $this->getTable('aw_ca_role')],
            'aw_role_table.id = main_table.' . CompanyUserInterface::COMPANY_ROLE_ID,
            [
                'role_name' => 'aw_role_table.name'
            ]
        );
        $this->getSelect()->joinLeft(
            ['aw_ca_unit_table' => $this->getTable('aw_ca_unit')],
            'aw_ca_unit_table.id = main_table.' . CompanyUserInterface::COMPANY_UNIT_ID,
            [
                'unit_name' => 'aw_ca_unit_table.unit_title'
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
     * @param CompanyUserInterface[] $items
     * @return $this
     */
    public function setItems(?array $items = null): self
    {
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_name') {
            $whereCondition = [
                $this->_translateCondition('customer_table.firstname', $condition),
                $this->_translateCondition('customer_table.lastname', $condition)
            ];
            $this->getSelect()->where(new \Zend_Db_Expr(implode(' OR ', $whereCondition)));

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
