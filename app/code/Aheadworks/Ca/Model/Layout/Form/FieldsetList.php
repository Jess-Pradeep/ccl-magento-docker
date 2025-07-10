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
namespace Aheadworks\Ca\Model\Layout\Form;

use Aheadworks\Ca\Model\Layout\Reader as LayoutReader;

/**
 * Class FieldsetList
 *
 * @package Aheadworks\Ca\Model\Layout\Form
 */
class FieldsetList
{
    /**
     * @var LayoutReader
     */
    private $layoutReader;

    /**
     * @var array
     */
    private $fieldsetList;

    /**
     * @param LayoutReader $layoutReader
     */
    public function __construct(
        LayoutReader $layoutReader
    ) {
        $this->layoutReader = $layoutReader;
    }

    /**
     * Get fieldset list
     *
     * @return array
     * @throws \Exception
     */
    public function get()
    {
        if ($this->fieldsetList === null) {
            $fieldsetData = $this->layoutReader->readFromFrontend(
                'aw_ca_company_ui_form',
                '//referenceContainer[@name="content"]/block[@name="aw.ca.company.form"]/arguments'
                . '/argument[@name="jsLayout"]/item[@name="components"]/item[@name="awCaForm"]/item[@name="children"]'
            );
            unset($fieldsetData['send']);
            unset($fieldsetData['extra_form']);
            $this->fieldsetList = array_keys($fieldsetData);
        }

        return $this->fieldsetList;
    }
}
