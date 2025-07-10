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
 * @package    QuickOrderGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrderGraphQl\Model\ProductList\ItemDataProcessor;

/**
 * Class Items
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList
 */
class Items implements ResolverInterface
{
    /**
     * @var ItemDataProcessor
     */
    private $itemDataProcessor;

    /**
     * @param ItemDataProcessor $itemDataProcessor
     */
    public function __construct(
        ItemDataProcessor $itemDataProcessor
    ) {
        $this->itemDataProcessor = $itemDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!isset($value[ProductListInterface::ITEMS])) {
            throw new LocalizedException(__('"items" value should be specified'));
        }
        $items = $value[ProductListInterface::ITEMS];

        $itemsData = [];
        $websiteId = $context->getExtensionAttributes()->getStore()->getWebsiteId();
        foreach ($items as &$item) {
            $itemsData[] = $this->itemDataProcessor->process($item, $websiteId);;
        }

        return $itemsData;
    }
}
