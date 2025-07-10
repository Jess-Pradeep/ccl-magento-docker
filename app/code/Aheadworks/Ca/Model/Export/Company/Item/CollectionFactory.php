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

namespace Aheadworks\Ca\Model\Export\Company\Item;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Export\Config as ExportConfig;
use Aheadworks\Ca\Model\Export\ExportEntity;
use Aheadworks\Ca\Model\Export\FilterProcessorAggregator;
use Aheadworks\Ca\Model\Export\Item\CollectionFactoryInterface;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection as CompanyCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager as ModuleManager;
use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Export;

/**
 * Export company item collection factory class
 */
class CollectionFactory implements CollectionFactoryInterface
{
    /**
     * @param FilterProcessorAggregator $filterProcessor
     * @param CompanyCollectionFactory $collectionFactory
     * @param ExportConfig $exportConfig
     */
    public function __construct(
        private readonly FilterProcessorAggregator $filterProcessor,
        private readonly CompanyCollectionFactory $collectionFactory,
        private readonly ExportConfig $exportConfig,
        private readonly ModuleManager $moduleManager
    ) {
    }

    /**
     * Create company item filtered collection
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return AbstractCollection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): AbstractCollection
    {
        $collection = $this->prepareEntityCollection();
        foreach ($this->retrieveFilterData($filters) as $filterName => $filterValue) {
            $attribute = $attributeCollection->getItemById($filterName);
            if (!$attribute) {
                throw new LocalizedException(
                    __('Given filter name "%1" is not present in collection.', $filterName)
                );
            }

            $frontendInput = $attribute->getData('frontend_input');
            if (!$frontendInput) {
                throw new LocalizedException(
                    __('There is no frontend input specified for filter "%1".', $filterName)
                );
            }

            $this->filterProcessor->process(
                $frontendInput,
                $collection,
                'main_table.' . $filterName,
                $filterValue
            );
        }

        return $collection;
    }

    /**
     * Prepare entity collection
     *
     * @return CompanyCollection
     */
    private function prepareEntityCollection(): CompanyCollection
    {
        /** @var CompanyCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns($this->exportConfig->prepareColumnsToSelect(ExportEntity::COMPANY_ENTITY_TYPE, 'company'))
            ->joinLeft(
                ['aw_ca_company_user' => $collection->getTable('aw_ca_company_user')],
                'aw_ca_company_user.company_id = main_table.id',
                $this->exportConfig->prepareColumnsToSelect(ExportEntity::COMPANY_ENTITY_TYPE, 'company_admin')
            )->joinLeft(
                ['customer_entity' => $collection->getTable('customer_entity')],
                'customer_entity.entity_id = aw_ca_company_user.customer_id',
                $this->exportConfig->prepareColumnsToSelect(ExportEntity::COMPANY_ENTITY_TYPE, 'root_company_user')
            )->where(CompanyUserInterface::IS_ROOT . ' = ?', 1);

        if ($this->moduleManager->isModuleEnabledByName('Aheadworks_CreditLimit')) {
            $collection->getSelect()->joinLeft(
                ['aw_cl_credit_summary' => $collection->getTable('aw_cl_credit_summary')],
                'aw_cl_credit_summary.company_id = main_table.id',
                ['credit_limit']
            );
        }

        return $collection;
    }

    /**
     * Retrieve filter data
     *
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters): array
    {
        return array_filter($filters[Export::FILTER_ELEMENT_GROUP] ?? [], fn ($value) => $value !== '');
    }
}
