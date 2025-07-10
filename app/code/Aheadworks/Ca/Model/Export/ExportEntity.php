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

namespace Aheadworks\Ca\Model\Export;

use Aheadworks\Ca\Model\Export\Item\CollectionFactoryInterface;
use Aheadworks\Ca\Model\Export\Item\ResultProcessor;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Export company accounts entity model
 */
class ExportEntity extends AbstractEntity
{
    public const COMPANY_ENTITY_TYPE = 'aw_ca_company';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param AttributeCollectionProviderInterface $attributeCollectionProvider
     * @param CollectionFactoryInterface $exportItemCollectionFactory
     * @param ColumnProvider $columnProvider
     * @param ResultProcessor $resultProcessor
     * @param string $entityType
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        private readonly AttributeCollectionProviderInterface $attributeCollectionProvider,
        private readonly CollectionFactoryInterface $exportItemCollectionFactory,
        private readonly ColumnProviderInterface $columnProvider,
        private readonly ResultProcessor $resultProcessor,
        private readonly string $entityType,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }

    /**
     * Entity attributes collection getter
     *
     * @return Collection
     * @throws Exception
     */
    public function getAttributeCollection(): Collection
    {
        return $this->attributeCollectionProvider->get();
    }

    /**
     * Export process
     *
     * @return string
     * @throws LocalizedException
     * @throws Exception
     */
    public function export(): string
    {
        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());

        $collection = $this->exportItemCollectionFactory->create(
            $this->getAttributeCollection(),
            $this->_parameters
        );

        foreach ($collection->getItems() as $item) {
            $writer->writeRow($this->resultProcessor->processResult($this->columnProvider->getColumns(), $item));
        }

        return $writer->getContents();
    }

    /**
     * Get header columns
     *
     * @return array
     */
    protected function _getHeaderColumns(): array
    {
        return $this->columnProvider->getHeaders();
    }

    /**
     * Export one item
     *
     * @param AbstractModel $item
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function exportItem($item): void
    {
        // will not implement this method as it is legacy interface
    }

    /**
     * Entity type code getter
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return $this->entityType;
    }

    /**
     * Get entity collection
     *
     * @return void
     */
    protected function _getEntityCollection(): void
    {
        // will not implement this method as it is legacy interface
    }
}
