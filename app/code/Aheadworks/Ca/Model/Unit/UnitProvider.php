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

namespace Aheadworks\Ca\Model\Unit;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Model\ResourceModel\Unit\Collection;
use Aheadworks\Ca\Model\ResourceModel\Unit\CollectionFactory as UnitCollectionFactory;

class UnitProvider
{
    public function __construct(
        private readonly UnitCollectionFactory $collectionFactory
    ) {
    }

    /**
     * Get Units for Company
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Model\ResourceModel\Unit\Collection
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function getUnitsForCompany(int $companyId):\Aheadworks\Ca\Model\ResourceModel\Unit\Collection
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter("company_id", $companyId);
        $collection->addOrder(UnitInterface::SORT_ORDER, Collection::SORT_ORDER_ASC);
        return $collection;
    }
}