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
namespace Aheadworks\Ctq\Model\Quote\Cart;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Cart\Converter as CartConverter;

/**
 * Class ToNativeCart
 * @package Aheadworks\Ctq\Model\Quote\Cart
 */
class ToNativeCart
{
    /**
     * @var CartConverter
     */
    private $cartConverter;

    /**
     * @var Converter
     */
    private $quoteCartConverter;

    /**
     * @param CartConverter $cartConverter
     * @param Converter $quoteCartConverter
     */
    public function __construct(
        CartConverter $cartConverter,
        Converter $quoteCartConverter
    ) {
        $this->cartConverter = $cartConverter;
        $this->quoteCartConverter = $quoteCartConverter;
    }

    /**
     * Convert cart to quote
     *
     * @param QuoteCartInterface $cartQuote
     * @param bool $isNew
     * @return CartInterface|Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($cartQuote, $isNew = false)
    {
        $cartQuoteArray = $this->quoteCartConverter->toArray($cartQuote);
        $cart = $this->cartConverter->toObject($cartQuoteArray, $isNew);

        return $cart;
    }
}
