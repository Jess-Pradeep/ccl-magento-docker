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

namespace Aheadworks\Ca\Model\Export\Config;

use Aheadworks\Ca\Model\ThirdPartyModule\Manager as ModuleManager;
use DOMDocument;
use DOMElement;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Coverts data from XML to array
 */
class Converter implements ConverterInterface
{
    /**
     * @param BooleanUtils $booleanUtils
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        private readonly BooleanUtils $booleanUtils,
        private readonly ModuleManager $moduleManager
    ) {
    }

    /**
     * Convert config
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source): array
    {
        $output = [];

        /** @var $entity DOMElement */
        foreach ($source->getElementsByTagName('entity') as $entity) {
            $fieldsetData = [];
            foreach ($entity->getElementsByTagName('fieldset') as $fieldset) {
                $attributes = [];
                /** @var $attribute DOMElement */
                foreach ($fieldset->getElementsByTagName('attribute') as $attribute) {
                    $dependentModule = $attribute->getAttribute('dependentModule');
                    if ($dependentModule && !$this->moduleManager->isModuleEnabledByName($dependentModule)) {
                        continue;
                    }

                    $attributeData = [
                        'code' => $attribute->getAttribute('code'),
                        'is_filterable' => $this->booleanUtils->toBoolean($attribute->getAttribute('is_filterable')),
                        'title' => $attribute->getAttribute('title'),
                        'dataType' => $attribute->getAttribute('dataType'),
                        'frontendInput' => $attribute->getAttribute('frontendInput'),
                        'sourceModel' => $attribute->getAttribute('sourceModel')
                    ];

                    $attributes[] = $attributeData;
                }

                $fieldsetArray = [
                    'csv_field_prefix' => $fieldset->getAttribute('csvFieldPrefix'),
                    'table_name' => $fieldset->getAttribute('tableName'),
                    'attributes' => $attributes
                ];

                $fieldsetData[$fieldset->getAttribute('name')] = $fieldsetArray;
            }

            $output[$entity->getAttribute('type')] = $fieldsetData;
        }

        return $output;
    }
}
