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
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\QuickOrder\Model\Config as QuickOrderConfig;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class Config implements ResolverInterface
{
    /**
     * @var QuickOrderConfig
     */
    private $quickOrderConfig;

    /**
     * @param QuickOrderConfig $quickOrderConfig
     */
    public function __construct(
        QuickOrderConfig $quickOrderConfig
    ) {
        $this->quickOrderConfig = $quickOrderConfig;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $websiteId = $args['websiteId'] ?? null;
        return [
            'is_quick_order_enabled' => $this->quickOrderConfig->isEnabled($websiteId),
            'is_add_to_list_button_displayed' => $this->quickOrderConfig->isAddToListButtonDisplayed($websiteId),
            'is_qty_input_displayed' => $this->quickOrderConfig->isQtyInputDisplayed($websiteId)
        ];
    }
}
