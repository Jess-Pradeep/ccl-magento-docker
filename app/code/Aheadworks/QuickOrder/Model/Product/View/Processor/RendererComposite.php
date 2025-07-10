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

namespace Aheadworks\QuickOrder\Model\Product\View\Processor;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\View as ProductViewBlock;

/**
 * Class RendererComposite
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor
 */
class RendererComposite
{
    /**
     * Base block
     */
    private const BASE_BLOCK = 'product.info.options.wrapper';
    private const BASE_BLOCK_TEMPLATE = 'Aheadworks_QuickOrder::product/popup.phtml';

    /**
     * @param array $rendererList
     */
    public function __construct(
        private array $rendererList = []
    ) {}

    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @return string
     */
    public function render(LayoutInterface $layout): string
    {
        $block = $this->getOptionsBlock($layout);
        foreach ($this->rendererList as $renderer) {
            $renderer->render($layout, $block, $block->getProduct());
        }

        return $block->toHtml();
    }

    /**
     * Get options block
     *
     * @param LayoutInterface $layout
     * @return Template
     */
    private function getOptionsBlock(LayoutInterface $layout): Template
    {
        $block = $layout->getBlock(self::BASE_BLOCK);
        if (!$block instanceof Template) {
            $block = $layout->createBlock(
                ProductViewBlock::class,
                self::BASE_BLOCK
            );
        }

        $block->setTemplate(self::BASE_BLOCK_TEMPLATE);

        return $block;
    }
}
