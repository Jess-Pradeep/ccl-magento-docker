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
namespace Aheadworks\Ctq\Block\Checkout\Cart;

use Magento\Checkout\Block\Cart\Sidebar as CheckoutSidebar;

/**
 * Class Sidebar
 * @package Aheadworks\Ctq\Block\Checkout\Cart
 * @method \Aheadworks\Ctq\ViewModel\LayoutModifier getLayoutModifier()
 */
class Sidebar extends CheckoutSidebar
{
    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getLayoutModifier()->prepareJsLayout($this->jsLayout);

        return parent::getJsLayout();
    }
}
