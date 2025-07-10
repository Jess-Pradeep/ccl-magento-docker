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
namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Address;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Catalog\Model\Product\Type as ProductType;

class Calculator
{
    /**
     * @var TaxConfig
     */
    private $taxConfig;

    /**
     * @param TaxConfig $taxConfig
     */
    public function __construct(
        TaxConfig $taxConfig
    ) {
        $this->taxConfig = $taxConfig;
    }

    /**
     * Get subtotal
     *
     * @param Quote $quote
     * @return float
     */
    public function getSubtotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal();
    }

    /**
     * Get estimated tax total
     *
     * @param Quote $quote
     * @return float
     */
    public function getEstimatedTaxTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getTaxAmount();
    }

    /**
     * Get negotiated discount amount
     *
     * @param Quote $quote
     * @return float
     */
    public function getNegotiatedDiscountAmount($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getAwCtqAmount();
    }

    /**
     * Get discount amount
     *
     * @param Quote $quote
     * @return float
     */
    public function getDiscountAmount($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getDiscountAmount();
    }

    /**
     * Calculate item cost
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemCost(Item $item)
    {
        $totalCost = 0;
        $children = $item->getChildren();
        if (is_array($children) && count($children)) {
            foreach ($children as $child) {
                $cost = floatval($child->getProduct()->getCost());
                $totalCost += $cost * $child->getQty();
            }
            return $totalCost;
        } else {
            $totalCost = floatval($item->getProduct()->getCost());
        }

        return $totalCost;
    }

    /**
     * Calculate item cart price
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemCartPrice(Item $item)
    {
        $discount = $item->getDiscountAmount() / $item->getQty();
        $tax = $this->taxConfig->priceIncludesTax($item->getStoreId())
            ? $item->getTaxAmount() / $item->getQty() + $item->getDiscountTaxCompensationAmount() / $item->getQty()
            : 0;

        return $item->getCalculationPrice() + $tax - $discount;
    }

    /**
     * Calculate item row subtotal with tax and discount
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemRowSubtotal(Item $item)
    {
        return $item->getRowTotal()
            - $item->getDiscountAmount()
            - $item->getAwCtqAmount()
            + $item->getTaxAmount()
            + $item->getDiscountTaxCompensationAmount();
    }

    /**
     * Calculate item proposed price
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemProposedPrice(Item $item)
    {
        $negotiatedDiscount = $this->getItemDiscountAmount($item) / $item->getQty();
        $cartPrice = $this->calculateItemCartPrice($item);
        return $cartPrice - $negotiatedDiscount;
    }

    /**
     * Calculate item negotiated discount
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemNegotiatedDiscount(Item $item)
    {
        return $this->getItemDiscountAmount($item);
    }

    /**
     * Calculate total cost
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateTotalCost($quote)
    {
        $totalCost = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $totalCost += $this->calculateItemCost($item) * $item->getQty();
        }
        return $totalCost;
    }

    /**
     * Calculate catalog price total excluding tax
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateCatalogPriceTotalExlTax($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal() + $address->getDiscountAmount();
    }

    /**
     * Calculate catalog price total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateCatalogPriceTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        $tax = $this->taxConfig->priceIncludesTax($quote->getStoreId())
            ? $address->getTaxAmount()
                + $address->getDiscountTaxCompensationAmount()
                - $address->getShippingTaxAmount()
            : 0;

        return $address->getSubtotal() + $tax + $address->getDiscountAmount();
    }

    /**
     * Calculate base catalog price total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateBaseCatalogPriceTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        $baseTax = $this->taxConfig->priceIncludesTax($quote->getStoreId())
            ? $address->getBaseTaxAmount()
                + $address->getBaseDiscountTaxCompensationAmount()
                - $address->getBaseShippingTaxAmount()
            : 0;

        return $address->getBaseSubtotal() + $baseTax + $address->getBaseDiscountAmount();
    }

    /**
     * Calculate negotiated quote total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateNegotiatedQuoteTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal()
            + $address->getDiscountAmount()
            + $address->getAwCtqAmount()
            + $address->getDiscountTaxCompensationAmount();
    }

    /**
     * Calculate base negotiated quote total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateBaseNegotiatedQuoteTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getBaseSubtotal()
            + $address->getBaseDiscountAmount()
            + $address->getBaseAwCtqAmount()
            + $address->getBaseDiscountTaxCompensationAmount();
    }

    /**
     * Calculate subtotal with discount and tax
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateSubtotalInclDiscountAndTax($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal()
            + $address->getTaxAmount()
            + $address->getDiscountAmount()
            + $address->getDiscountTaxCompensationAmount()
            + $address->getAwCtqAmount();
    }

    /**
     * Get quote address
     *
     * @param Quote $quote
     * @return Address
     */
    private function getQuoteAddress($quote)
    {
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        return $address;
    }

    /**
     * Get discount amount for quote item
     *
     * @param Item $item
     * @return float
     */
    private function getItemDiscountAmount(Item $item)
    {
        if ($item->getProductType() != ProductType::TYPE_BUNDLE) {
            return $item->getAwCtqAmount();
        }

        $negotiatedDiscount = 0.0;
        foreach ($item->getChildren() as $child) {
            $negotiatedDiscount += $child->getAwCtqAmount();
        }

        return $negotiatedDiscount;
    }
}
