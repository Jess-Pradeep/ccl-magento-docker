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

namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;

class MergeManager
{
    /**
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param ProductListRepositoryInterface $productListRepository
     * @param null $lastMergedItemKey
     */
    public function __construct(
        private ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        private ProductListRepositoryInterface $productListRepository,
        private $lastMergedItemKey = null
    ) {}

    /**
     * Get prepared array with merged items
     *
     * @param array $newItems
     * @param array $listItems
     * @return array
     */
    public function mergeItems(array $newItems, array $listItems = []): array
    {
        $newItems = $this->mergeNewItems($newItems);
        $items = $this->prepareItems($newItems, $listItems);
        foreach ($newItems as $itemToMerge) {
            if ($preparedItem = $this->getPreparedCandidateToMerge($itemToMerge, $listItems)) {
                unset($items[$itemToMerge->getItemKey()]);
                $items[$preparedItem->getItemKey()] = $preparedItem;
                $this->setLastMergedItemKey($preparedItem->getItemKey());
            }
        }

        return $items;
    }

    /**
     * Get prepared candidate to merge
     *
     * @param ProductListItemInterface $itemToMerge
     * @param array $listItems
     * @return ProductListItemInterface|null
     */
    public function getPreparedCandidateToMerge(
        ProductListItemInterface $itemToMerge,
        array $listItems = []
    ): ?ProductListItemInterface {
        if (!$listItems && $itemToMerge->getListId()) {
            $list = $this->productListRepository->get($itemToMerge->getListId());
            $listItems = $list->getItems();
        }
        foreach ($listItems as $candidateToMerge) {
            if ($this->itemsCanBeMerged($itemToMerge, $candidateToMerge)) {
                return $candidateToMerge->setProductQty(
                    $itemToMerge->getProductQty() + $candidateToMerge->getProductQty()
                );
            }
        }

        return null;
    }

    /**
     * Is items can be merged
     *
     * @param ProductListItemInterface $itemToMerge
     * @param ProductListItemInterface $candidateToMerge
     * @return bool
     */
    private function itemsCanBeMerged(
        ProductListItemInterface $itemToMerge, ProductListItemInterface $candidateToMerge)
    : bool {
        return $itemToMerge->getItemKey() != $candidateToMerge->getItemKey()
            && $itemToMerge->getProductId() == $candidateToMerge->getProductId()
            && $this->isIdenticalOptions($itemToMerge, $candidateToMerge);
    }

    /**
     * Prepare items for processing
     *
     * @param array $newItems
     * @param array $listItems
     * @return array
     */
    private function prepareItems(array $newItems, array $listItems): array
    {
        $itemsList = [];
        $items = array_merge($listItems, $newItems);
        foreach ($items as $item) {
            $itemsList[$item->getItemKey()] = $item;
        }

        return $itemsList;
    }

    /**
     * Merge new items together
     *
     * @param array $newItems
     * @return array
     */
    private function mergeNewItems(array $newItems): array
    {
        $mergedItems = [];
        $mergedItemsKeys = [];
        foreach ($newItems as $itemToMerge) {
            foreach ($newItems as $candidateToMerge) {
                if (!in_array($itemToMerge->getItemKey(), $mergedItemsKeys)
                    && $this->itemsCanBeMerged($itemToMerge, $candidateToMerge))
                {
                    $mergedItemsKeys[] = $candidateToMerge->getItemKey();
                    $itemToMerge->setProductQty(
                        $itemToMerge->getProductQty() + $candidateToMerge->getProductQty()
                    );
                }
            }
            if (!in_array($itemToMerge->getItemKey(), $mergedItemsKeys)) {
                $mergedItems[] = $itemToMerge;
            }
        }

        return $mergedItems;
    }

    /**
     * Is item`s have identical options
     *
     * @param ProductListItemInterface $itemToMerge
     * @param ProductListItemInterface $listItem
     * @return bool
     */
    private function isIdenticalOptions(ProductListItemInterface $itemToMerge, ProductListItemInterface $listItem): bool
    {
        $itemToMergeOptions = $this->extensibleDataObjectConverter->toNestedArray(
            $itemToMerge->getProductOption(),
            [],
            ProductOptionInterface::class
        );
        $listItemOptions = $this->extensibleDataObjectConverter->toNestedArray(
            $listItem->getProductOption(),
            [],
            ProductOptionInterface::class
        );

        return $itemToMergeOptions == $listItemOptions;
    }

    /**
     * Set last merged item key
     *
     * @param string $itemKey
     * @return void
     */
    private function setLastMergedItemKey(string $itemKey): void
    {
        $this->lastMergedItemKey = $itemKey;
    }

    /**
     * Get last merged item key
     *
     * @return string|null
     */
    public function getLastMergedItemKey(): ?string
    {
        return $this->lastMergedItemKey;
    }
}
