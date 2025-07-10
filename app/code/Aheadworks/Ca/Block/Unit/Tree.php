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

namespace Aheadworks\Ca\Block\Unit;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Model\ResourceModel\Unit\Collection;
use Aheadworks\Ca\Model\ResourceModel\Unit\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\SerializerInterface as JsonSerializer;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Model\Unit\UnitProvider;

class Tree extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Ca::unit/tree.phtml';

    /**
     * Tree Construct
     *
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param JsonSerializer $jsonSerializer
     * @param UnitRepositoryInterface $unitRepository
     * @param UnitProvider $unitProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly CollectionFactory $collectionFactory,
        private readonly JsonSerializer $jsonSerializer,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly UnitProvider $unitProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve units data for tree
     *
     * @return array
     */
    private function getUnits(): array
    {
        $units = [];
        $currentUnitId = $this->getRequest()->getParam('id', 0);
        $parentUnitId = $this->getRequest()->getParam('parent', 0);
        $companyId = $this->getCompanyId();
        /** @var Collection $collection */
        $collection = $this->unitProvider->getUnitsForCompany($companyId);

        /** @var UnitInterface $unit */
        foreach ($collection->getItems() as $unit) {
            $units[] = [
                'id' => $unit->getId(),
                'parent' => $unit->getParentId() ? $unit->getParentId() : '#',
                'text' => $this->formatUnitName($unit->getUnitTitle()),
                'data' => [
                    'sort_order' => $unit->getSortOrder()
                ],
                'state' => [
                    'selected' => ($unit->getId() == $currentUnitId || $unit->getId() == $parentUnitId),
                    'opened' => !$unit->getParentId()
                ],
                'a_attr' => [
                    'href' => $this->getUrl('*/*/edit', ['id' => $unit->getId(), 'company_id' => $companyId])
                ]
            ];
        }

        return $units;
    }

    /**
     * Get Company Id
     *
     * @return int
     */
    private function getCompanyId()
    {
        return (int)$this->getRequest()->getParam('company_id', 0);
    }

    /**
     * Format unit name
     *
     * @param string $name
     * @return string
     */
    private function formatUnitName($name): string
    {
        $name = str_replace("'", '&#39;', $name);
        return $name;
    }

    /**
     * Get Root Unit Id
     *
     * @return int
     */
    private function getRootUnitId()
    {
        $rootUnit = $this->unitRepository->getCompanyRootUnit($this->getCompanyId());
        return $rootUnit->getId();
    }
    
    /**
     * Retrieve config
     *
     * @return string
     */
    public function getConfig(): string
    {
        return $this->jsonSerializer->serialize([
            'units' => $this->getUnits(),
            'moveUrl' => $this->getUrl('*/*/move'),
            'deleteUrl' => $this->getUrl('*/*/delete'),
            'rootUnitId' => $this->getRootUnitId()
        ]);
    }
}
