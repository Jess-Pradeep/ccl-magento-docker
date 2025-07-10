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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Model\Service;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Collection;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Comparator;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class RequisitionListService
 *
 * @package Aheadworks\RequisitionLists\Model\Service
 */
class RequisitionListService implements RequisitionListManagementInterface
{
    /**
     * @param CollectionFactory $itemCollectionFactory
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param Comparator $itemComparator
     * @param ProductRepositoryInterface $productRepository
     * @param OptionConverter $converter
     * @param RequisitionListItemInterfaceFactory $listItemFactory
     * @param DataObjectFactory $objectFactory
     */
    public function __construct(
        private readonly CollectionFactory $itemCollectionFactory,
        private readonly RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        private readonly Comparator $itemComparator,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly OptionConverter $converter,
        private readonly RequisitionListItemInterfaceFactory $listItemFactory,
        private readonly DataObjectFactory $objectFactory
    ) {
    }

    /**
     * Add item to list
     *
     * @param RequisitionListItemInterface $item
     * @return RequisitionListItemInterface
     * @throws CouldNotSaveException
     */
    public function addItem(RequisitionListItemInterface $item): RequisitionListItemInterface
    {
        $foundItem = $this->getItemRepresentation($item);
        if ($foundItem) {
            $foundItem->setProductQty($foundItem->getProductQty() + $item->getProductQty());
            $item = $foundItem;
        }

        return $this->requisitionListItemRepository->save($item);
    }

    /**
     * Update item option
     *
     * @param int $itemId
     * @param array $buyRequest
     *
     * @return RequisitionListItemInterface
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateItemOption(int $itemId, array $buyRequest): RequisitionListItemInterface
    {
        $requestItem = $this->listItemFactory->create();
        $item = $this->requisitionListItemRepository->get($itemId);
        $product = $this->productRepository->getById($item->getProductId());

        $productOption = $this->converter->toProductOptionObject($item->getProductType(), $buyRequest);
        $requestItem->setProductOption($productOption);

        if (!$requestItem->getProductOption()) {
            if (!$item->getProductOption()) {
                $option = $this->converter->toProductOptionObject($product->getTypeId(), []);
                $item->setProductOption($option);
            }
        } else {
            $item->setProductOption($requestItem->getProductOption());
        }

        $foundItem = $this->getItemRepresentation($item);

        if ($foundItem) {
            $foundItem->setProductQty($foundItem->getProductQty() + $item->getProductQty());

            $this->requisitionListItemRepository->delete($item);

            return $this->requisitionListItemRepository->save($foundItem);
        }

        $request = $this->objectFactory->create();
        $request->addData($buyRequest);
        $listCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, 'full');

        if (gettype($listCandidates) !== 'string' && $product->getSku() !== $item->getProductSku()) {
            $item->setProductSku($product->getSku());
        }

        return $this->requisitionListItemRepository->save($item);
    }

    /**
     * @inheritdoc
     */
    public function moveItem($item, $listIdToMove)
    {
        $this->requisitionListItemRepository->delete($item);
        $item->setItemId(null);
        $item->setListId($listIdToMove);
        return $this->addItem($item);
    }

    /**
     * Get item representation
     *
     * @param RequisitionListItemInterface $item
     * @return RequisitionListItemInterface|null
     */
    private function getItemRepresentation($item)
    {
        /** @var Collection $itemCollection */
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->addFieldToFilter(
            RequisitionListItemInterface::LIST_ID,
            ['eq' => $item->getListId()]
        );
        /** @var RequisitionListItemInterface[] $listItems */
        $listItems = $itemCollection->getItems();
        foreach ($listItems as $listItem) {
            if ($this->itemComparator->compareIfEqual($listItem, $item)) {
                return $listItem;
            }
        }

        return null;
    }
}
