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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Model\Product\Option\Configurable;

use Magento\ConfigurableProduct\Api\Data\ConfigurableItemOptionValueInterface;
use Magento\Framework\DataObject;
use Magento\ConfigurableProduct\Model\ProductOptionProcessor;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Processor
 * @package Aheadworks\RequisitionLists\Model\Product\Option\Configurable
 */
class Processor extends ProductOptionProcessor
{
    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $options = $this->getConfigurableItemOptions($productOption);
        if (!empty($options)) {
            $requestData = [];
            foreach ($options as $option) {
                /** @var ConfigurableItemOptionValueInterface $option */
                $requestData['super_attribute'][$option->getOptionId()] = (string)$option->getOptionValue();
            }
            $request->addData($requestData);
        }

        return $request;
    }
}
