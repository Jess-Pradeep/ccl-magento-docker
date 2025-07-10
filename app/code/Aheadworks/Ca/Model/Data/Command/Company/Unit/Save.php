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

namespace Aheadworks\Ca\Model\Data\Command\Company\Unit;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Api\Data\UnitInterfaceFactory;
use Aheadworks\Ca\Api\UnitRepositoryInterface;

class Save implements CommandInterface
{
    public const CURRENT_COMPANY_ID ='current_company_id';
    
    /**
     * Save Construct
     *
     * @param DataObjectHelper $dataObjectHelper
     * @param UnitRepositoryInterface $unitRepository
     * @param UnitInterfaceFactory $unitDataFactory
     */
    public function __construct(
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly UnitInterfaceFactory $unitDataFactory,
    ) {
    }

    /**
     * Save unit information
     *
     * @param mixed $unitData
     * @return mixed
     */
    public function execute(mixed $unitData):mixed
    {
        /** @var UnitInterface $unitObject */
        $unitId = $unitData['id'];
        unset($unitData['id']);
        $unitObject = $unitId
        ? $this->unitRepository->get($unitId)
        : $this->unitDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $unitObject,
            $unitData,
            UnitInterface::class
        );
        $unitObject->setCompanyId($unitData[self::CURRENT_COMPANY_ID]);
        return $this->unitRepository->save($unitObject);
    }
}
