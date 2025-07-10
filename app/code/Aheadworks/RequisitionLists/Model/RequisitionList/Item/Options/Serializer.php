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
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options;

use Magento\Framework\DataObject;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Api\Data\ProductOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Serializer
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item\Option
 */
class Serializer
{
    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @param JsonSerializer $serializer
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ProductOptionInterfaceFactory $productOptionFactory
     */
    public function __construct(
        JsonSerializer $serializer,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        ProductOptionInterfaceFactory $productOptionFactory
    ) {
        $this->serializer = $serializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->productOptionFactory = $productOptionFactory;
    }

    /**
     * Convert product option to serialized string
     *
     * @param ProductOptionInterface $productOption
     * @return bool|false|string
     */
    public function serializeToString($productOption)
    {
        $objectData = $this->dataObjectProcessor->buildOutputDataArray(
            $productOption,
            ProductOptionInterface::class
        );

        return $this->serializer->serialize($objectData);
    }

    /**
     * Convert serialized string to product option object
     *
     * @param string $serializedString
     * @return ProductOptionInterface
     */
    public function unserializeToObject($serializedString)
    {
        /** @var ProductOptionInterface $object */
        $object = $this->productOptionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->serializer->unserialize($serializedString),
            ProductOptionInterface::class
        );

        return $object;
    }

    /**
     * Convert serialized string to data array
     *
     * @param string $serializedString
     * @return array
     */
    public function unserializeToDataArray($serializedString)
    {
        return $this->serializer->unserialize($serializedString);
    }
}
