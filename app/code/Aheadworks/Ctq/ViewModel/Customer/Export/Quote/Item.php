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
namespace Aheadworks\Ctq\ViewModel\Customer\Export\Quote;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Calculator as TotalCalculator;

/**
 * Class Item
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Export\Quote
 */
class Item implements ArgumentInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var TotalCalculator
     */
    private $totalCalculator;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param TotalCalculator $totalCalculator
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        TotalCalculator $totalCalculator
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->totalCalculator = $totalCalculator;
    }

    /**
     * Get total calculator
     *
     * @return TotalCalculator
     */
    public function getTotalCalculator()
    {
        return $this->totalCalculator;
    }

    /**
     * Return rounded price
     *
     * @param float $price
     * @return false|float
     */
    public function getRoundedPrice($price)
    {
        return $this->priceCurrency->round($price);
    }

    /**
     * Retrieve formatted price
     *
     * @param float $value
     * @param int $storeId
     * @return string
     */
    public function formatPrice($value, $storeId)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $storeId
        );
    }

    /**
     * Retrieve currency symbol
     *
     * @param Quote $quote
     * @return string
     */
    public function getCurrencySymbol($quote)
    {
        return $this->priceCurrency->getCurrencySymbol(null, $quote->getCurrency()->getBaseCurrencyCode());
    }
}
