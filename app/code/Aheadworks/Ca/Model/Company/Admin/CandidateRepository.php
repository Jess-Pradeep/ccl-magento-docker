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

namespace Aheadworks\Ca\Model\Company\Admin;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\CompanyAdminCandidateRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateSearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Company\Admin\Candidate as CompanyAdminCandidateModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate as CompanyAdminCandidateResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate\Collection as CompanyAdminCandidateCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\Admin\Candidate\CollectionFactory as CompanyAdminCandidateCollectionFactory;

class CandidateRepository implements CompanyAdminCandidateRepositoryInterface
{
    /**
     * @var array
     */
    private array $registry = [];

    /**
     * @param CompanyAdminCandidateResourceModel $resource
     * @param CompanyAdminCandidateInterfaceFactory $companyAdminCandidateFactory
     * @param CompanyAdminCandidateCollectionFactory $companyAdminCandidateCollectionFactory
     * @param CompanyAdminCandidateSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        private readonly CompanyAdminCandidateResourceModel $resource,
        private readonly CompanyAdminCandidateInterfaceFactory $companyAdminCandidateFactory,
        private readonly CompanyAdminCandidateCollectionFactory $companyAdminCandidateCollectionFactory,
        private readonly CompanyAdminCandidateSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly JoinProcessorInterface $extensionAttributesJoinProcessor,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly DataObjectHelper $dataObjectHelper
    ) {
    }

    /**
     * Save candidate
     *
     * @param CompanyAdminCandidateInterface $candidate
     * @return CompanyAdminCandidateInterface
     * @throws CouldNotSaveException
     */
    public function save(CompanyAdminCandidateInterface $candidate): CompanyAdminCandidateInterface
    {
        try {
            $this->resource->save($candidate);
            $this->registry[$candidate->getId()] = $candidate;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $candidate;
    }

    /**
     * Retrieve candidate by ID
     *
     * @param int $candidateId
     * @return CompanyAdminCandidateInterface
     * @throws NoSuchEntityException
     */
    public function get(int $candidateId): CompanyAdminCandidateInterface
    {
        if (!isset($this->registry[$candidateId])) {
            /** @var CompanyAdminCandidateInterface $candidate */
            $candidate = $this->companyAdminCandidateFactory->create();
            $this->resource->load($candidate, $candidateId);
            if (!$candidate->getId()) {
                throw NoSuchEntityException::singleField(CompanyAdminCandidateInterface::ID, $candidateId);
            }
            $this->registry[$candidateId] = $candidate;
        }

        return $this->registry[$candidateId];
    }

    /**
     * Retrieve candidate list matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CompanyAdminCandidateSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CompanyAdminCandidateCollection $collection */
        $collection = $this->companyAdminCandidateCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CompanyAdminCandidateInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CompanyAdminCandidateSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var CompanyAdminCandidateModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Delete candidate
     *
     * @param CompanyAdminCandidateInterface $candidate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CompanyAdminCandidateInterface $candidate): bool
    {
        try {
            $this->resource->delete($candidate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$candidate->getId()])) {
            unset($this->registry[$candidate->getId()]);
        }

        return true;
    }

    /**
     * Delete candidate by ID
     *
     * @param int $candidateId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $candidateId): bool
    {
        return $this->delete($this->get($candidateId));
    }

    /**
     * Retrieves data object using model
     *
     * @param CompanyAdminCandidateModel $model
     * @return CompanyAdminCandidateInterface
     */
    private function getDataObject(CompanyAdminCandidateModel $model): CompanyAdminCandidateInterface
    {
        /** @var CompanyAdminCandidateInterface $object */
        $object = $this->companyAdminCandidateFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            CompanyAdminCandidateInterface::class
        );

        return $object;
    }
}
