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

use Aheadworks\Ca\Model\Export\Config as ExportConfig;
use Exception;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;

/**
 * Provides collection with attributes
 */
class AttributeCollectionProvider implements AttributeCollectionProviderInterface
{
    /**
     * @var Collection
     */
    private Collection $collection;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @param ExportConfig $exportConfig
     * @param string $entityType
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly AttributeFactory $attributeFactory,
        private readonly ExportConfig $exportConfig,
        private readonly string $entityType
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
    }

    /**
     * Get attribute collection
     *
     * @return Collection
     * @throws Exception
     */
    public function get(): Collection
    {
        if (count($this->collection->getItems()) == 0) {
            $filterableAttributes = $this->exportConfig->getFilterableAttributes($this->entityType);
            foreach ($filterableAttributes as $filterableAttribute) {
                $frontendInput = $filterableAttribute['frontendInput'];
                $attribute = $this->attributeFactory->create();
                $attribute->setId($filterableAttribute['code']);
                $attribute->setDefaultFrontendLabel(__($filterableAttribute['title']));
                $attribute->setAttributeCode($filterableAttribute['code']);

                $attribute->setFrontendInput($frontendInput);
                if ($frontendInput === 'select' || $frontendInput === 'multiselect') {
                    $attribute->setBackendType('int');
                    $attribute->setSourceModel($filterableAttribute['sourceModel']);
                }

                if ($frontendInput === 'text') {
                    $attribute->setBackendType($frontendInput);
                }

                $this->collection->addItem($attribute);
            }
        }

        return $this->collection;
    }
}
