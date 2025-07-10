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

namespace Aheadworks\Ca\Model\ResourceModel\HistoryLog\Grid;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Ca\Model\ResourceModel\HistoryLog\Collection as HistoryLogCollection;
use Aheadworks\Ca\Model\ResourceModel\HistoryLog as HistoryLogResourceModel;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;

/**
 * Class Collection
 */
class Collection extends HistoryLogCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyManagement;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param UserContextInterface $userContext
     * @param SellerCompanyManagementInterface $sellerCompanyManagement
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        UserContextInterface $userContext,
        SellerCompanyManagementInterface $sellerCompanyManagement,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        $this->userContext = $userContext;
        $this->sellerCompanyManagement = $sellerCompanyManagement;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Document::class, HistoryLogResourceModel::class);
    }

    /**
     * Init select
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $customerId = $this->userContext->getUserId();
        $company = $this->sellerCompanyManagement->getCompanyByCustomerId($customerId);
        $this->addFieldToFilter('company_id', $company->getId());

        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\Search\DocumentInterface[] $items
     * @return $this
     */
    public function setItems(?array $items = null)
    {
        return $this;
    }

    /**
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
     * @return \Magento\Framework\Api\Search\SearchCriteriaInterface|null
     */
    public function getSearchCriteria(): ?SearchCriteriaInterface
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * Get total count.
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
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
