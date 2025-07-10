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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Layout\Customer\QuoteList\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Ctq\Model\Layout\ProcessorInterface;
use Aheadworks\Ctq\Model\Magento\VersionManager;

/**
 * Class CartItemRenderer
 *
 * @package Aheadworks\Ctq\Model\Layout\Customer\QuoteList\Processor
 */
class CartItemRenderer implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @param ArrayManager $arrayManager
     * @param VersionManager $versionManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        VersionManager $versionManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->versionManager = $versionManager;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        if ($this->versionManager->isNeedToUpdateCartItemRenderer()) {
            $itemRendererPath = $this->arrayManager->findPath('item.renderer', $jsLayout);
            if ($itemRendererPath) {
                $jsLayout = $this->arrayManager->merge(
                    $itemRendererPath,
                    $jsLayout,
                    [
                        'config' => [
                            'component' => 'Magento_Checkout/js/view/cart-item-renderer'
                        ]
                    ]
                );
            }
        }

        return $jsLayout;
    }
}
