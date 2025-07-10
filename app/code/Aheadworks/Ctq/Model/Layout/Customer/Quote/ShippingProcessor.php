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

namespace Aheadworks\Ctq\Model\Layout\Customer\Quote;

use Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider as QuoteDataProvider;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;

class ShippingProcessor implements LayoutProcessorInterface
{
    /**
     * @param ArrayManager $arrayManager
     * @param QuoteDataProvider $quoteDataProvider
     */
    public function __construct(
        private readonly ArrayManager $arrayManager,
        private readonly QuoteDataProvider $quoteDataProvider,
    ) {
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function process($jsLayout)
    {
        $shippingPath = 'components/block-totals/children/shipping';
        $shippingLayout = $this->arrayManager->get($shippingPath, $jsLayout);
        if ($shippingLayout) {
            $shippingLayout = $this->prepareShippingRender($shippingLayout);
            $jsLayout = $this->arrayManager->set($shippingPath, $jsLayout, $shippingLayout);
        }

        return $jsLayout;
    }

    /**
     * Prepare shipping render
     *
     * @param array $shippingLayout
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function prepareShippingRender(array $shippingLayout): array
    {
        $cart = $this->quoteDataProvider->getCart();
        if ($cart->isVirtual()) {
            $addressShippingMethod = $cart->getBillingAddress()->getShippingMethod();
        } else {
            $addressShippingMethod = $cart->getShippingAddress()->getShippingMethod();
        }
        if ($addressShippingMethod) {
            $shippingLayout['isNeedPriceRender'] = true;
        }

        return $shippingLayout;
    }
}
