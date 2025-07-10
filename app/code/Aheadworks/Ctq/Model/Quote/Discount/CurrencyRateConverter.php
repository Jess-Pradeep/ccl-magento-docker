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
namespace Aheadworks\Ctq\Model\Quote\Discount;

use Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType;
use Aheadworks\Ctq\Model\Converter\RateConverter;

/**
 * Class CurrencyRateConverter
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount
 */
class CurrencyRateConverter
{
    /**
     * @var RateConverter
     */
    private $rateConverter;

    /**
     * @param RateConverter $rateConverter
     */
    public function __construct(
        RateConverter $rateConverter
    ) {
        $this->rateConverter = $rateConverter;
    }

    /**
     * Convert amount to currency
     *
     * @param string $type
     * @param float $value
     * @param string $currencyCodeFrom
     * @param string $currencyCodeTo
     * @return float
     * @throws \Exception
     */
    public function convertAmountValueToCurrency($type, $value, $currencyCodeFrom, $currencyCodeTo)
    {
        if ($type
            && in_array($type, [DiscountType::PROPOSED_PRICE, DiscountType::AMOUNT_DISCOUNT])
        ) {
            $value = $this->rateConverter->convertAmount(
                $value,
                $currencyCodeFrom,
                $currencyCodeTo
            );
        }

        return $value;
    }
}
