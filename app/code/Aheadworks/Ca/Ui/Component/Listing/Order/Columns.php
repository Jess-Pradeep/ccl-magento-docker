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
namespace Aheadworks\Ca\Ui\Component\Listing\Order;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Config;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns as MagentoColumns;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class Columns
 * @package Aheadworks\Ca\Ui\Component\Listing\Order
 */
class Columns extends MagentoColumns
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param AttributeRepositoryInterface $attributeRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        AttributeRepositoryInterface $attributeRepository,
        CompanyUserManagementInterface $companyUserManagement,
        array $components = [],
        array $data = []
    ) {
        $this->config = $config;
        $this->attributeRepository = $attributeRepository;
        $this->companyUserManagement = $companyUserManagement;
        parent::__construct($context, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function getChildComponents()
    {
        if ($this->companyUserManagement->getCurrentUser()) {
            $this->addCreatedByColumn();
        }

        return parent::getChildComponents();
    }

    /**
     * Add 'Created By' column
     *
     * @return void
     */
    private function addCreatedByColumn()
    {
        $column = $this->getComponent('customer_name');
        $data = $column->getData('config');
        $data['visible'] = true;
        $column->setData('config', $data);
    }
}
