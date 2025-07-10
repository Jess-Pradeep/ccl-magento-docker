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

namespace Aheadworks\Ca\Model\ResourceModel\Order\Grid;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as MagentoOrderCollection;
use Psr\Log\LoggerInterface as Logger;
use Magento\Sales\Model\Order\Config;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends MagentoOrderCollection
{
    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param UserContextInterface $userContext
     * @param Config $orderConfig
     * @param StoreManagerInterface $storeManager
     * @param AppState $appState
     * @param RequestInterface $request
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        private readonly AuthorizationManagementInterface $authorizationManagement,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly UserContextInterface $userContext,
        private readonly Config $orderConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly AppState $appState,
        private readonly RequestInterface $request,
        $mainTable = 'sales_order_grid',
        $resourceModel = Order::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * Init select
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND) {
            $customers = [];
            $customerId = $this->userContext->getUserId();
            if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_sales_sub_view')) {
                $customers = $this->companyUserManagement->getChildUsersIds($customerId);
            }
            $customers[] = $customerId;
            $this->addFilters($customers);

            $this->_eventManager->dispatch(
                'awCa_init_order_history_collection_after',
                ['collection' => $this]
            );
        } elseif ($customerId = $this->getCustomerIdByCompanyId()) {
            $customers = $this->companyUserManagement->getChildUsersIds($customerId);
            $customers[] = $customerId;
            $this->addFilters($customers);
        }

        return $this;
    }

    /**
     * Add filters
     *
     * @param array $customers
     * @return void
     * @throws NoSuchEntityException
     */
    private function addFilters(array $customers = []): void
    {
        $this
            ->addFieldToFilter('customer_id', ['in' => $customers])
            ->addFieldToFilter(
                'store_id',
                $this->storeManager->getStore()->getId()
            )->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            );
    }

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerIdByCompanyId(): ?int
    {
        $companyId = $this->request->getParam('company_id');
        if (!$companyId) {
            return null;
        }
        $rootUser = $this->companyUserManagement->getRootUserForCompany($companyId);

        return (int)$rootUser->getId();
    }
}
