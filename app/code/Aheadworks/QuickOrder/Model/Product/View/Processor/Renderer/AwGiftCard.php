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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class AwGiftCard extends AbstractRenderer implements RendererInterface
{
    /**
     * Aheadworks Gift Card product type ID
     */
    const TYPE_ID = 'aw_giftcard';

    /**
     * Gift card block with fields
     */
    const GIFT_CARD_OPTIONS_BLOCK = 'product.info.giftcard.options';

    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @param ProductInterface $product
     * @return $this
     */
    public function render($layout, $block, $product)
    {
        if ($product->getTypeId() !== self::TYPE_ID) {
            return $this;
        }

        $giftcardOptionsBlock = $layout->getBlock(self::GIFT_CARD_OPTIONS_BLOCK);
        if ($giftcardOptionsBlock instanceof Template) {
            $this->appendBlock($block, $giftcardOptionsBlock, 'aw_giftcard_options');
        }

        return $this;
    }
}
