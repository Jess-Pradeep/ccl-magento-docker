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
declare(strict_types=1);

namespace Aheadworks\Ctq\ViewModel\Quote\Edit;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterfaceFactory;
use Magento\Quote\Model\Quote\Address\Rate;

class ShippingRender implements ArgumentInterface
{
    const PATH_QUOTE_SHIPPING_FORM = '\Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping\Method\Form';

    /**
     * @param LayoutInterfaceFactory $layoutFactory
     * @param array $customRate
     * @param array $nativeRate
     */
    public function __construct(
        private readonly LayoutInterfaceFactory $layoutFactory,
        private readonly array $customRate,
        private readonly array $nativeRate
    ) {}

    /**
     * Get shipping rate
     *
     * @param Rate $rate
     * @return string
     */
    public function getShippingRate(Rate $rate): string
    {
        $arguments['data'] = ['rate' => $rate];

        if (isset($this->customRate[$rate->getCarrier()])) {
            $renderBlock = $this->layoutFactory->create()->createBlock(self::PATH_QUOTE_SHIPPING_FORM, '', $arguments);
            $renderBlock->setTemplate($this->customRate['aw_ctq_custom']);

            return $renderBlock->toHtml();
        }
        $renderBlock = $this->layoutFactory->create()->createBlock(self::PATH_QUOTE_SHIPPING_FORM, '', $arguments);
        $renderBlock->setTemplate($this->nativeRate['native']);

        return $renderBlock->toHtml();
    }

    /**
     * Get active shipping rate
     *
     * @param Rate $rate
     * @return string
     */
    public function getActiveShippingRate(Rate $rate): string
    {
        $arguments['data'] = ['rate' => $rate];

        if (isset($this->customRate[$rate->getCarrier()])) {
            $renderBlock = $this->layoutFactory->create()->createBlock(self::PATH_QUOTE_SHIPPING_FORM, '', $arguments);
            $renderBlock->setTemplate($this->customRate['aw_ctq_custom_active']);

            return $renderBlock->toHtml();
        }
        $renderBlock = $this->layoutFactory->create()->createBlock(self::PATH_QUOTE_SHIPPING_FORM, '', $arguments);
        $renderBlock->setTemplate($this->nativeRate['native_active']);

        return $renderBlock->toHtml();
    }
}
