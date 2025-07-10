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
namespace Aheadworks\Ca\Model\Layout\Form\Customization;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Block\Adminhtml\System\Config\Form\Field\Customization as FieldCustomization;

/**
 * Class Applier
 *
 * @package Aheadworks\Ca\Model\Layout\Form\Customization
 */
class Applier
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply customization to form field
     *
     * @param string $fieldsetName
     * @param string $fieldName
     * @param array $fieldConfig
     * @return array
     */
    public function apply($fieldsetName, $fieldName, $fieldConfig)
    {
        $customization = $this->config->getFieldsetFieldsCustomization($fieldsetName, $this->getStoreId());
        if (empty($customization)) {
            return $fieldConfig;
        }

        $rowsCustom = $customization[FieldCustomization::ROWS] ?? [];
        if (isset($rowsCustom[$fieldName]['sort_order'])) {
            $fieldConfig['sortOrder'] = (string)($rowsCustom[$fieldName]['sort_order'] * 10);
        }

        $fieldsCustom = $customization[FieldCustomization::FIELDS] ?? [];
        if (isset($fieldsCustom[$fieldName]['label'])) {
            $fieldConfig['label'] = __($fieldsCustom[$fieldName]['label']);
        }
        if (isset($fieldsCustom[$fieldName]['visible'])) {
            $fieldConfig['visible'] = (bool)$fieldsCustom[$fieldName]['visible'];
        }
        if (isset($fieldsCustom[$fieldName]['required'])) {
            $fieldConfig['validation']['required-entry'] = (bool)($fieldsCustom[$fieldName]['required']);
        }

        return $fieldConfig;
    }

    /**
     * Get store ID
     *
     * @return int
     */
    private function getStoreId()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $exception) {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        return $storeId;
    }
}
