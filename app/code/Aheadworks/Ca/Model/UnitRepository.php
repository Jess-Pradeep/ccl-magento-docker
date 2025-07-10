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

use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Api\Data\UnitInterfaceFactory;
use Aheadworks\Ca\Api\Data\UnitSearchResultsInterfaceFactory;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Model\ResourceModel\Unit as ResourceUnit;
use Aheadworks\Ca\Model\ResourceModel\Unit\CollectionFactory as UnitCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaInterface;

class UnitRepository implements UnitRepositoryInterface
{
    /**
     * UnitRepository Construct
     *
     * @param ResourceUnit $resource
     * @param UnitInterfaceFactory $unitFactory
     * @param UnitCollectionFactory $unitCollectionFactory
     * @param UnitSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceUnit $resource,
        private readonly UnitInterfaceFactory $unitFactory,
        private readonly UnitCollectionFactory $unitCollectionFactory,
        private readonly UnitSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * Save Unit
     *
     * @param UnitInterface $unit
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function save(UnitInterface $unit)
    {
        try {
            $this->resource->save($unit);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the unit: %1',
                $exception->getMessage()
            ));
        }
        return $unit;
    }

    /**
     * Retrieve Unit
     *
     * @param int $unitId
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function get($unitId)
    {
        $unit = $this->unitFactory->create();
        $this->resource->load($unit, $unitId);
        if (!$unit->getId()) {
            throw new NoSuchEntityException(__('Unit with id "%1" does not exist.', $unitId));
        }
        return $unit;
    }

    /**
     * Retrieve Company Root Unit Id
     *
     * @param int $companyId
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function getCompanyRootUnit($companyId)
    {
        $rooUnitId = $this->resource->getCompanyRootUnit($companyId);
        if (!$rooUnitId) {
            throw NoSuchEntityException::singleField('id', $rooUnitId);
        }

        return $this->get($rooUnitId);
    }

    /**
     * Retrieve Unit matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnitSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->unitCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Unit
     *
     * @param UnitInterface $unit
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(UnitInterface $unit)
    {
        try {
            $unitModel = $this->unitFactory->create();
            $this->resource->load($unitModel, $unit->getId());
            $this->resource->delete($unitModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Unit: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Unit by ID
     *
     * @param int $unitId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($unitId)
    {
        $this->removeAllChild($unitId);
        return $this->delete($this->get($unitId));
    }

    /**
     * Removes all child units from parent
     *
     * @param int $unitId
     * @throws NoSuchEntityException
     */
    private function removeAllChild($unitId)
    {
        $childUnits = $this->resource->getAllChildUnits($unitId);
        if (isset($childUnits) && count($childUnits)) {
            foreach ($childUnits as $childUnit) {
                $childUnitId = $childUnit['id'];
                if ($childUnitId == $unitId) {
                    continue;
                }
                $this->delete($this->get($childUnitId));
            }
        }
    }
}
