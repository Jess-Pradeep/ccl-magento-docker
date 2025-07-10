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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Grid;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection as CompanyCollection;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;

class Collection extends CompanyCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var array
     */
    private $additionalColumns = [
        'is_root' => 'company_user.is_root',
        'customer_id' => 'company_user.customer_id',
        'customer_phone' => 'company_user.telephone',
        'customer_name' => "CONCAT(customer.firstname, ' ', customer.lastname)",
        'customer_email' => 'customer.email',
        'customer_group' => 'customer.group_id',
        'customer_job' => 'company_user.job_title',
        'website_id' => 'customer.website_id',
    ];

    /**
     * Grid Collection Construct
     *
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AuthSession $authSession
     * @param AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param array $additionalColumns
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        private readonly AuthSession $authSession,
        $eventPrefix,
        $eventObject,
        $additionalColumns = [],
        $model = Document::class,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->additionalColumns = array_merge(
            $this->additionalColumns,
            $additionalColumns
        );
        $this->setModel($model);
    }

    /**
     * Get Aggregation
     *
     * @return AggregationInterface
     */
    public function getAggregations()
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
     * Get Search Criteria
     *
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set Search Criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(?SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get Total Count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set Total Count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set Items
     *
     * @param array|null $items
     * @return $this
     */
    public function setItems(?array $items = null)
    {
        return $this;
    }

    /**
     * Init Select
     *
     * @return $this|CompanyCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinInner(
            ['company_user' => $this->getTable(CompanyUser::MAIN_TABLE_NAME)],
            'company_user.' . CompanyUserInterface::COMPANY_ID . ' = main_table.' . CompanyInterface::ID
                . ' AND company_user.is_root = 1',
            []
        );
        $this->getSelect()->joinInner(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = company_user.' . CompanyUserInterface::CUSTOMER_ID,
            []
        );
        $this->getSelect()->columns($this->additionalColumns);

        foreach ($this->additionalColumns as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }
        $this->addRoleWebsiteFilter();
        return $this;
    }

    /**
     * Add website Filter for Current Role
     *
     * @return void
     */
    private function addRoleWebsiteFilter()
    {
        $extensionUser = $this->authSession->getUser();
        $role = $extensionUser->getRole()->getData();
        if (isset($role["gws_is_all"]) && !$role["gws_is_all"]) {
            $this->addFieldToFilter('website_id', ['in' => $role["gws_websites"]]);
        }
    }

    /**
     * Apply filter based on conditions
     *
     * @param string $field
     * @param string $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'customer_name') {
            $whereCondition = [
                $this->_translateCondition('customer.firstname', $condition),
                $this->_translateCondition('customer.lastname', $condition)
            ];
            $this->getSelect()->where(new \Zend_Db_Expr(implode(' OR ', $whereCondition)));

            return $this;
        }

        if (isset($this->additionalColumns[$field])) {
            $this->addFilterToMap($field, $this->additionalColumns[$field]);
        } else {
            $this->createMainTableFieldAlias($field);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Create main field alias
     *
     * @param string|array $field
     */
    private function createMainTableFieldAlias($field)
    {
        if (is_array($field)) {
            foreach ($field as $filterItem) {
                $this->addFilterToMap($filterItem, 'main_table.' . $filterItem);
            }
        } else {
            $this->addFilterToMap($field, 'main_table.' . $field);
        }
    }
}
