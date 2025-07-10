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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Model\Layout\Form\FieldsetList;
use Aheadworks\Ca\Model\Layout\Form\Customization\Applier;

/**
 * Class Customization
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class Customization implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FieldsetList
     */
    private $fieldsetList;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * @param ArrayManager $arrayManager
     * @param FieldsetList $fieldsetList
     * @param Applier $applier
     */
    public function __construct(
        ArrayManager $arrayManager,
        FieldsetList $fieldsetList,
        Applier $applier
    ) {
        $this->arrayManager = $arrayManager;
        $this->fieldsetList = $fieldsetList;
        $this->applier = $applier;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function modifyMeta(array $meta)
    {
        $fieldsetList = $this->fieldsetList->get();
        foreach ($fieldsetList as $fieldsetName) {
            $fieldsetPath = $this->arrayManager->findPath($fieldsetName, $meta);
            $fields = $this->arrayManager->get($fieldsetPath . '/children', $meta);
            foreach ($fields as $fieldName => $field) {
                $fields[$fieldName] = $this->applier->apply($fieldsetName, $fieldName, $field);
            }

            $meta = $this->arrayManager->merge($fieldsetPath . '/children', $meta, $fields);
        }

        return $meta;
    }
}
