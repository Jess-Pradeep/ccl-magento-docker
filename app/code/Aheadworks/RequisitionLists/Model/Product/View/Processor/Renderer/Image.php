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
namespace Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer;

use Aheadworks\RequisitionLists\Block\Product\Renderer\Image as ProductImage;

/**
 * Class Image
 * @package Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer
 */
class Image implements RendererInterface
{
    /**
     * @inheritdoc
     */
    public function render($layout, $block, $product)
    {
        $imageBlock = $layout->createBlock(
            ProductImage::class,
            'aw_qo.popup.product-image',
            ['data' => ['product' => $product]]
        );
        $block->append($imageBlock, 'product_image');

        return $this;
    }
}
