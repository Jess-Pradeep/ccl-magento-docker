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
namespace Aheadworks\Ctq\Model\Converter;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Directory\Model\Currency;

/**
 * Class RateConverter
 *
 * @package Aheadworks\Ctq\Model\Converter
 */
class RateConverter
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Convert amount from one currency to another
     *
     * @param float $amount
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     * @throws \Exception
     */
    public function convertAmount($amount, $currencyFrom, $currencyTo)
    {
        if ($currencyFrom != $currencyTo) {
            /** @var Currency $actionFromCurrency */
            $actionFromCurrency = $this->priceCurrency->getCurrency(null, $currencyFrom);
            if (!$actionFromCurrency->getRate($currencyTo)) {
                /** @var Currency $actionToCurrency */
                $actionToCurrency = $this->priceCurrency->getCurrency(null, $currencyTo);
                if ($actionToCurrency->getRate($currencyFrom)) {
                    $rate = 1 / $actionToCurrency->getRate($currencyFrom);
                    $actionFromCurrency->setRates([$currencyTo => $rate]);
                }
            }

            if ($actionFromCurrency->getRate($currencyTo)) {
                $amount = $this->priceCurrency->round($actionFromCurrency->convert($amount, $currencyTo));
            }
        }

        return $amount;
    }
}
