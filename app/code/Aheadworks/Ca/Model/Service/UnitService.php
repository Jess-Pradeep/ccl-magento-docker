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

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\Data\UnitInterfaceFactory;
use Aheadworks\Ca\Api\UnitManagementInterface;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\DataObjectFactory;
use Aheadworks\Ca\Model\Config;

class UnitService implements UnitManagementInterface
{
    /**
     * UnitService Constructor
     *
     * @param UnitRepositoryInterface $unitRepository
     * @param UnitInterfaceFactory $unitFactory
     * @param Config $config
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly UnitInterfaceFactory $unitFactory,
        private readonly Config $config,
        private readonly DataObjectFactory $dataObjectFactory
    ) {
    }

    /**
     * Create default unit
     *
     * @param int $companyId
     * @return UnitInterface
     * @throws CouldNotSaveException
     */
    public function createDefaultUnit(int $companyId):\Aheadworks\Ca\Api\Data\UnitInterface
    {
        $unitTitle = $this->config->getHeadUnitTitle();
        $unitDescription = $this->config->getHeadUnitDescription();
        $unit = $this->unitFactory
            ->create()
            ->setUnitTitle($unitTitle)
            ->setUnitDescription($unitDescription)
            ->setCompanyId($companyId)
            ->setParentId(0)
            ->setSortOrder(0);
        return $this->unitRepository->save($unit);
    }

    /**
     * Move Unit
     *
     * @param string[] $unitsData
     * @return void
     */
    public function moveUnit(array $unitsData)
    {
        foreach ($unitsData as $unitData) {
            $unitDataObject = $this->dataObjectFactory->create(['data' => $unitData]);
            $targetId = (int)$unitDataObject->getTargetId();
            $parentId = (int)$unitDataObject->getParentId();
            $sortOrder = (int)$unitDataObject->getSortOrder();
            $path = $unitDataObject->getPath();
            $unit = $this->unitRepository->get($targetId);
            $unit
                ->setParentId($parentId)
                ->setSortOrder($sortOrder)
                ->setPath($path);
            $this->unitRepository->save($unit);
        }
    }
}
