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
namespace Aheadworks\Ca\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Aheadworks\Ca\Model\Layout\Form\Customization\FieldsProviderInterface;

/**
 * Class Customization
 *
 * @package Aheadworks\Ca\Block\Adminhtml\System\Config\Form\Field
 */
class Customization extends Field
{
    const FIELDS = 'fields';
    const ROWS = 'rows';

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Ca::system/config/form/customization.phtml';

    /**
     * @var FieldsProviderInterface
     */
    private $fieldsProvider;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param Context $context
     * @param FieldsProviderInterface $fieldsProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        FieldsProviderInterface $fieldsProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->fieldsProvider = $fieldsProvider;
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Get fields
     *
     * @return array
     * @throws \Exception
     */
    public function getFields()
    {
        if ($this->fields === null) {
            $this->fields = $this->fieldsProvider->get();
        }

        return $this->fields;
    }

    /**
     * Get fields
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldRows()
    {
        $data = [];
        foreach ($this->getFields() as $fieldName => $fieldConfig) {
            $data[$fieldName] = $fieldName;
        }
        uksort($data, [$this, 'compareFieldRows']);
        return $data;
    }

    /**
     * Get field sort order
     *
     * @param string $fieldName
     * @return int|bool
     * @throws \Exception
     */
    public function getFieldSortOrder($fieldName)
    {
        $value = $this->getElement()->getValue();
        if (isset($value[self::ROWS][$fieldName])) {
            return $value[self::ROWS][$fieldName]['sort_order'];
        } else {
            $fields = $this->getFields();
            if (array_key_exists($fieldName, $fields) && $fields[$fieldName]['sortOrder']) {
                return $fields[$fieldName]['sortOrder'];
            }

            return 1;
        }
    }

    /**
     * Compare field row IDs by sort order
     *
     * @param string $rowId1
     * @param string $rowId2
     * @return int
     * @throws \Exception
     */
    private function compareFieldRows($rowId1, $rowId2)
    {
        $row1SortOrder = $this->getFieldSortOrder($rowId1);
        $row2SortOrder = $this->getFieldSortOrder($rowId2);

        if ($row1SortOrder > $row2SortOrder) {
            return 1;
        } elseif ($row1SortOrder < $row2SortOrder) {
            return -1;
        }

        return 0;
    }

    /**
     * Get form field saved config
     *
     * @param string $fieldName
     * @return array
     * @throws \Exception
     */
    public function getFormFieldSavedConfig($fieldName)
    {
        $value = $this->getElement()->getValue();
        if (isset($value[self::FIELDS][$fieldName])) {
            return $value[self::FIELDS][$fieldName];
        } else {
            return $this->getFormFieldDefaultConfig($fieldName);
        }
    }

    /**
     * Get form field default config
     *
     * @param string $fieldName
     * @return array
     * @throws \Exception
     */
    public function getFormFieldDefaultConfig($fieldName)
    {
        $fields = $this->getFields();
        $defaultConfig = [];
        if (array_key_exists($fieldName, $fields)) {
            $fieldConfig = $fields[$fieldName];
            $defaultConfig = [
                'visible' => true,
                'required' => isset($fieldConfig['validation']['required-entry'])
                    && ($fieldConfig['validation']['required-entry']),
                'label' => $fieldConfig['label']
            ];
        }

        return $defaultConfig;
    }

    /**
     * Check if modification of metadata field is allowed
     *
     * @param string $fieldName
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function canModifyMeta($fieldName, $name)
    {
        $fields = $this->getFields();
        $result = true;
        if (array_key_exists($fieldName, $fields)) {
            if (isset($fields[$fieldName]['customization-restrictions'][$name])) {
                $result = $fields[$fieldName]['customization-restrictions'][$name];
            }
        }

        return $result;
    }

    /**
     * Get metadata fields with boolean values
     *
     * @return array
     */
    public function getBooleanMetaFields()
    {
        return [
            'visible' => __('Enable'),
            'required' => __('Required')
        ];
    }

    /**
     * Get base html Id
     *
     * @return string
     */
    public function getHtmlId()
    {
        $htmlId = $this->getData('html_id');
        if (!$htmlId) {
            $htmlId = '_' . uniqid('', false);
            $this->setData('html_id', $htmlId);
        }
        return $htmlId;
    }

    /**
     * Get input html ID
     *
     * @param string $fieldName
     * @param string $metaField
     * @return string
     */
    public function getInputHtmlId($fieldName, $metaField)
    {
        $htmlId = $this->getHtmlId() . '-field-' . $fieldName;
        $htmlId .= '-' . $metaField;
        return $htmlId;
    }

    /**
     * Get input html name
     *
     * @param string $fieldName
     * @param string $metaField
     * @param string $part
     * @return string
     */
    public function getInputHtmlName($fieldName, $metaField, $part = self::FIELDS)
    {
        $htmlName = $this->getElement()->getName() . '[' . $part . '][' . $fieldName . ']';
        $htmlName .= '[' . $metaField . ']';
        return $htmlName;
    }
}
