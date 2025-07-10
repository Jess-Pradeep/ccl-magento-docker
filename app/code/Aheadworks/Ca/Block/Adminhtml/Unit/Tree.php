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

namespace Aheadworks\Ca\Block\Adminhtml\Unit;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Model\ResourceModel\Unit\Collection;
use Aheadworks\Ca\Model\ResourceModel\Unit\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Serialize\SerializerInterface as JsonSerializer;
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
     * @param UnitProvider $unitProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly CollectionFactory $collectionFactory,
        private readonly JsonSerializer $jsonSerializer,
        private readonly UnitProvider $unitProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Add Tree Buttons
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addTreeButtons();
        return parent::_prepareLayout();
    }

    /**
     * Add tree buttons
     */
    private function addTreeButtons()
    {
        $currentId = $this->getRequest()->getParam('id', 0);
        $companyId = $this->getRequest()->getParam('company_id', 0);
        $parentId = $this->getRequest()->getParam('parent', 0);
        $parentId = $parentId ? $parentId : $currentId;

        $this->addChild(
            'add_sub_button',
            Button::class,
            [
                'label' => __('Add Subunit'),
                'class' => 'add',
                'onclick' => sprintf(
                    'window.location.href = "%s"',
                    $this->getUrl('*/*/new', ['parent' => $parentId, 'company_id' => $companyId])
                ),
                'id' => 'add_unit_button'
            ]
        );
    }

    /**
     * Retrieve sub unit button
     *
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * Retrieve units data for tree
     *
     * @return array
     */
    private function getUnits(): array
    {
        $units = [];
        $currentUnitId = (int)$this->getRequest()->getParam('id', 0);
        $parentUnitId = (int)$this->getRequest()->getParam('parent', 0);
        $companyId = (int)$this->getRequest()->getParam('company_id', 0);
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
     * Retrieve config
     *
     * @return string
     */
    public function getConfig(): string
    {
        return $this->jsonSerializer->serialize([
            'units' => $this->getUnits(),
            'moveUrl' => $this->getUrl('*/*/move'),
            'isAdmin' => true
        ]);
    }
}
