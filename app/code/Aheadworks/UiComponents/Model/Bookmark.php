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
 * @package    UiComponents
 * @version    1.0.5
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\UiComponents\Model;

use Aheadworks\UiComponents\Model\ResourceModel\Bookmark as ResourceBookmark;
use Aheadworks\UiComponents\Model\ResourceModel\Bookmark\Collection;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class Bookmark
 * @package Aheadworks\UiComponents\Model
 */
class Bookmark extends \Magento\Ui\Model\Bookmark
{
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        ResourceBookmark $resource,
        Collection $resourceCollection,
        DecoderInterface $jsonDecoder,
        array $data = [],
        ?\Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory,
            $resource, $resourceCollection, $jsonDecoder, $data, $serializer);
    }
}
