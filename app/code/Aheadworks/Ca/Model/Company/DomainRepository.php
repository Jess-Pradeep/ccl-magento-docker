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
namespace Aheadworks\Ca\Model\Company;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanyDomainSearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Company\Domain as CompanyDomainModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain as CompanyDomainResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain\Collection as CompanyDomainCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain\CollectionFactory as CompanyDomainCollectionFactory;

/**
 * Class DomainRepository
 *
 * @package Aheadworks\Ca\Model\CompanyDomain
 */
class DomainRepository implements CompanyDomainRepositoryInterface
{
    /**
     * @var CompanyDomainResourceModel
     */
    private $resource;

    /**
     * @var CompanyDomainInterfaceFactory
     */
    private $companyDomainFactory;

    /**
     * @var CompanyDomainCollectionFactory
     */
    private $companyDomainCollectionFactory;

    /**
     * @var CompanyDomainSearchResultsInterfaceFactory
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
     * @var array
     */
    private $registry = [];

    /**
     * @param CompanyDomainResourceModel $resource
     * @param CompanyDomainInterfaceFactory $companyDomainFactory
     * @param CompanyDomainCollectionFactory $companyDomainCollectionFactory
     * @param CompanyDomainSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        CompanyDomainResourceModel $resource,
        CompanyDomainInterfaceFactory $companyDomainFactory,
        CompanyDomainCollectionFactory $companyDomainCollectionFactory,
        CompanyDomainSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->companyDomainFactory = $companyDomainFactory;
        $this->companyDomainCollectionFactory = $companyDomainCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function save(CompanyDomainInterface $domain)
    {
        try {
            $this->resource->save($domain);
            $this->registry[$domain->getId()] = $domain;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $domain;
    }

    /**
     * @inheritdoc
     */
    public function get($domainId, $reload = false)
    {
        if (!isset($this->registry[$domainId]) || $reload) {
            /** @var CompanyDomainInterface $domain */
            $domain = $this->companyDomainFactory->create();
            $this->resource->load($domain, $domainId);
            if (!$domain->getId()) {
                throw NoSuchEntityException::singleField(CompanyDomainInterface::ID, $domainId);
            }
            $this->registry[$domainId] = $domain;
        }

        return $this->registry[$domainId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CompanyDomainCollection $collection */
        $collection = $this->companyDomainCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CompanyDomainInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CompanyDomainSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var CompanyDomainModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(CompanyDomainInterface $domain)
    {
        try {
            $this->resource->delete($domain);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$domain->getId()])) {
            unset($this->registry[$domain->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($domainId)
    {
        return $this->delete($this->get($domainId));
    }

    /**
     * Retrieves data object using model
     *
     * @param CompanyDomainModel $model
     * @return CompanyDomainInterface
     */
    private function getDataObject($model)
    {
        /** @var CompanyDomainInterface $object */
        $object = $this->companyDomainFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            CompanyDomainInterface::class
        );

        return $object;
    }
}
