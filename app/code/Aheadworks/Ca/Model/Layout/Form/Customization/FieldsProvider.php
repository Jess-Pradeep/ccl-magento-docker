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

use Aheadworks\Ca\Model\Layout\Reader as LayoutReader;

/**
 * Class FieldsProvider
 *
 * @package Aheadworks\Ca\Model\Layout\Form\Customization
 */
class FieldsProvider implements FieldsProviderInterface
{
    /**
     * @var LayoutReader
     */
    private $layoutReader;

    /**
     * @var array
     */
    private $fieldsToExclude;

    /**
     * @var string
     */
    private $layout;

    /**
     * @var string
     */
    private $xpath;

    /**
     * @param LayoutReader $layoutReader
     * @param array $fieldsToExclude
     * @param string $layout
     * @param string $xpath
     */
    public function __construct(
        LayoutReader $layoutReader,
        $fieldsToExclude = [],
        $layout = '',
        $xpath = ''
    ) {
        $this->layoutReader = $layoutReader;
        $this->fieldsToExclude = $fieldsToExclude;
        $this->layout = $layout;
        $this->xpath = $xpath;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        $fields = $this->layout && $this->layoutReader
            ? $this->layoutReader->readFromFrontend($this->layout, $this->xpath)
            : [];
        foreach ($fields as $fieldName => $field) {
            if (in_array($fieldName, $this->fieldsToExclude)) {
                unset($fields[$fieldName]);
            }
        }

        return $fields;
    }
}
