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

namespace Aheadworks\Ctq\Block\Email\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\RendererList;
use Magento\Checkout\Block\Cart\Totals as CartTotalsBlock;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Get quote details for email
 *
 * @method ArgumentInterface|null getViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Quote\Provider getQuoteProviderViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Store\Provider getStoreProviderViewModel()
 */
class Details extends Template
{
    /**
     * Get item row html
     *
     * @param QuoteItem|CartItemInterface $item
     * @param string $itemType
     * @return string
     */
    public function getItemHtml($item, string $itemType): string
    {
        $itemHtml = '';
        /** @var RendererList $rendererList */
        $rendererList = $this->getChildBlock('item.list.renderer');
        if (!$rendererList) {
            throw new \RuntimeException(
                'Items list renderer for block "' . $this->getNameInLayout() . '" is not defined'
            );
        }
        $rendererBlock = $rendererList->getRenderer($itemType, 'default');
        if ($rendererBlock) {
            $rendererBlock
                ->setData('item', $item)
                ->setData('is_edit', false);
            $itemHtml = $rendererBlock->toHtml();
        }

        return $itemHtml;
    }

    /**
     * Retrieve totals html
     *
     * @param CartInterface|Quote $cart
     * @return string
     */
    public function getTotalsHtml($cart): string
    {
        /** @var CartTotalsBlock $totalsRenderer */
        $totalsRenderer = $this->getChildBlock('totals.renderer');
        if (!$totalsRenderer) {
            throw new \RuntimeException(
                'Totals renderer for block "' . $this->getNameInLayout() . '" is not defined'
            );
        }
        $totalsRenderer->setData('custom_quote', $cart);

        return $totalsRenderer->renderTotals(null, 3)
            . $totalsRenderer->renderTotals('footer', 3);
    }

    /**
     * Retrieve quote
     *
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function getQuote(): QuoteInterface
    {
        $quote = $this->getData('quote');
        if ($quote !== null) {
            return $quote;
        }

        $quoteId = (int)$this->getData('quote_id');
        $quote = $quoteId
            ? $this->getQuoteProviderViewModel()->getQuote($quoteId)
            : $this->getQuoteProviderViewModel()->getEmptyQuote();
        $this->setData('quote', $quote);

        return $this->getData('quote');
    }

    /**
     * Retrieve store
     *
     * @return StoreInterface
     * @throws LocalizedException
     */
    public function getStore(): StoreInterface
    {
        $store = $this->getData('store');
        if ($store !== null) {
            return $store;
        }

        $storeId = (int)$this->getData('store_id');
        $store = $storeId
            ? $this->getStoreProviderViewModel()->getStore($storeId)
            : $this->getStoreProviderViewModel()->getDefaultStore();
        $this->setData('store', $store);

        return $this->getData('store');
    }
}
