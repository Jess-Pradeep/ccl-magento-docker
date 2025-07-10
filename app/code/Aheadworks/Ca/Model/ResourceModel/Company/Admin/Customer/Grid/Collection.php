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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Admin\Customer\Grid;

use Magento\Customer\Model\ResourceModel\Grid\Collection as CustomerGridCollection;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\State as AppState;

class Collection extends CustomerGridCollection
{
    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param ResolverInterface $localeResolver
     * @param AppState $appState
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        ResolverInterface $localeResolver,
        protected readonly AppState $appState
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $localeResolver);
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     * @throws LocalizedException
     */
    public function addFieldToFilter($field, $condition = null): self
    {
        if (is_array($field)) {
            $field = reset($field);
        }
        if ($field === CompanyUserInterface::COMPANY_ID) {
            $companyField = 'aw_ca_company_user_table.' . $field;
            $this->getSelect()->joinLeft(
                ['aw_ca_company_user_table' => $this->getTable('aw_ca_company_user')],
                'aw_ca_company_user_table.customer_id = main_table.entity_id',
                [$companyField]
            );

            $andCondition = [
                $this->_getConditionSql($companyField, $condition),
                'aw_ca_company_user_table.is_root = 0'
            ];

            $whereCondition = [
                implode(' AND ', $andCondition)
            ];

            if ($this->appState->getAreaCode() == Area::AREA_ADMINHTML) {
                //gives backend admin right to select non-company users
                $whereCondition[] = $companyField . ' IS NULL';
            }

            $this->getSelect()->where(new \Zend_Db_Expr(implode(' OR ', $whereCondition)));
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
