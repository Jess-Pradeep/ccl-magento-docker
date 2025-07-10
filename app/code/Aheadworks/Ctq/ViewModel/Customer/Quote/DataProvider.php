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
namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Directory\Model\PriceCurrency;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class DataProvider
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class DataProvider implements ArgumentInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var PriceCurrency
     */
    private $priceCurrency;

    /**
     * @var Locator
     */
    private $locator;

    /**
     * @var string
     */
    private $methodToLocateQuote;

    /**
     * @param TimezoneInterface $timezone
     * @param PriceCurrency $priceCurrency
     * @param Locator $locator
     */
    public function __construct(
        TimezoneInterface  $timezone,
        PriceCurrency $priceCurrency,
        Locator $locator
    ) {
        $this->timezone = $timezone;
        $this->locator = $locator;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Retrieve active quote
     *
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function getQuote()
    {
        return $this->locator->locateQuote($this->getMethodToLocateQuote());
    }

    /**
     * Retrieve active quote ID
     *
     * @return int|null
     * @throws LocalizedException
     */
    public function getQuoteId()
    {
        $quote = $this->getQuote();
        return $quote->getId();
    }

    /**
     * Retrieve cart
     *
     * @return CartInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCart()
    {
        $quote = $this->getQuote();
        return $this->locator->getCartByQuote($quote);
    }

    /**
     * Get date of update
     *
     * @param Quote $quote
     * @return string
     * @throws \Exception
     */
    public function getUpdatedAt($quote)
    {
        $updatedAt = $quote->getUpdatedAt();
        if (!$updatedAt) {
            $updatedAt = $quote->getCreatedAt();
        }

        return $this->timezone->date(new \DateTime($updatedAt))->format('d-m-Y');
    }

    /**
     * Get current date
     *
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->timezone->date(new \DateTime())->format('d-m-Y');
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * Get total
     *
     * @param Quote $cart
     * @return float|string
     */
    public function getTotal($cart)
    {
        $totals = $cart->getTotals();
        $subtotal = $totals['subtotal'];

        return $this->priceCurrency->format($subtotal->getValue());
    }

    /**
     * Set method to locate quote
     *
     * @param string $method
     */
    public function setMethodToLocateQuote($method)
    {
        $this->methodToLocateQuote = $method;
    }

    /**
     * Get method to locate quote
     *
     * @return string
     */
    private function getMethodToLocateQuote()
    {
        return $this->methodToLocateQuote ?? Locator::LOCATE_BY_ID;
    }
}
