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
namespace Aheadworks\Ca\Model\Config\Backend\Customization;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Ca\Model\Config\Backend\Customization;
use Aheadworks\Ca\Block\Adminhtml\System\Config\Form\Field\Customization as FormCustomization;

/**
 * Class Validator
 *
 * @package Aheadworks\Ca\Model\Config\Backend\Customization
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if and only if value meets the validation requirements
     *
     * @param Customization $entity
     * @return bool
     */
    public function isValid($entity)
    {
        $this->_clearMessages();

        /** @var array $value */
        $value = $entity->getValue();
        foreach ($value[FormCustomization::FIELDS] as $fieldConfig) {
            if (isset($fieldConfig['label']) && empty($fieldConfig['label'])) {
                $this->_addMessages([__('Label is required.')]);
            }
        }

        return empty($this->getMessages());
    }
}
