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
namespace Aheadworks\Ctq\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * Class RequestQuoteLink
 * @method \Aheadworks\Ctq\ViewModel\LayoutModifier getLayoutModifier()
 */
class RequestQuoteLink extends Template
{
    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout['quote_id'] = $this->getViewModel()->getQuoteId();
        $this->jsLayout = $this->getLayoutModifier()->prepareJsLayout($this->jsLayout);
        unset($this->jsLayout['quote_id']);

        return parent::getJsLayout();
    }
}
