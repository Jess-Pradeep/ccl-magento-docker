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

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\HistoryLogRepositoryInterface;
use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Api\Data\HistoryLogInterfaceFactory;
use Aheadworks\Ca\Api\Data\HistoryLogSearchResultsInterface;
use Aheadworks\Ca\Api\Data\HistoryLogSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\ResourceModel\HistoryLog as HistoryLogResourceModel;
use Aheadworks\Ca\Model\ResourceModel\HistoryLog\CollectionFactory as HistoryLogCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class HistoryLogRepository
 */
class HistoryLogRepository implements HistoryLogRepositoryInterface
{
    /**
     * @var HistoryLogResourceModel
     */
    private $resource;

    /**
     * @var HistoryLogInterfaceFactory
     */
    private $historyLogInterfaceFactory;

    /**
     * @var HistoryLogCollectionFactory
     */
    private $historyLogCollectionFactory;

    /**
     * @var HistoryLogSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param HistoryLogResourceModel $resource
     * @param HistoryLogInterfaceFactory $historyLogInterfaceFactory
     * @param HistoryLogCollectionFactory $historyLogCollectionFactory
     * @param HistoryLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        HistoryLogResourceModel $resource,
        HistoryLogInterfaceFactory $historyLogInterfaceFactory,
        HistoryLogCollectionFactory $historyLogCollectionFactory,
        HistoryLogSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->historyLogInterfaceFactory = $historyLogInterfaceFactory;
        $this->historyLogCollectionFactory = $historyLogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save History Log
     *
     * @param HistoryLogInterface $historyLog
     * @return HistoryLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(HistoryLogInterface $historyLog): HistoryLogInterface
    {
        try {
            $this->resource->save($historyLog);
            $historyLogId = $historyLog->getId();
            $this->registry[$historyLogId] = $historyLog;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $historyLog;
    }

    /**
     * Retrieve History Log by id
     *
     * @param int $historyLogId
     * @return HistoryLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($historyLogId): HistoryLogInterface
    {
        if (!isset($this->registry[$historyLogId])) {
            /** @var HistoryLogInterface $historyLog */
            $historyLog = $this->historyLogInterfaceFactory->create();
            $this->resource->load($historyLog, $historyLogId);
            if (!$historyLog->getId()) {
                throw NoSuchEntityException::singleField('id', $historyLogId);
            }
            $this->registry[$historyLogId] = $historyLog;
        }
        return $this->registry[$historyLogId];
    }

    /**
     * Retrieve History Log list matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Aheadworks\Ca\Model\ResourceModel\HistoryLog\Collection $collection */
        $collection = $this->historyLogCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, HistoryLogInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var HistoryLogSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Company $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Delete History Log
     *
     * @param HistoryLogInterface $historyLog
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(HistoryLogInterface $historyLog): bool
    {
        try {
            $this->resource->delete($historyLog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$historyLog->getId()])) {
            unset($this->registry[$historyLog->getId()]);
        }

        return true;
    }

    /**
     * Delete History Log by ID
     *
     * @param int $historyLogId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($historyLogId): bool
    {
        return $this->delete($this->get($historyLogId));
    }

    /**
     * Retrieves data object using model
     *
     * @param HistoryLog $model
     * @return HistoryLogInterface
     */
    private function getDataObject($model): HistoryLogInterface
    {
        /** @var HistoryLogInterface $object */
        $object = $this->historyLogInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, HistoryLogInterface::class),
            HistoryLogInterface::class
        );
        return $object;
    }
}
