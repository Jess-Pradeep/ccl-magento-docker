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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Currency;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Currency;

/**
 * Class Manager
 */
class Manager
{
    /**
     * Manager constructor.
     *
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(private CurrencyFactory $currencyFactory)
    {
    }

    /**
     * Get formatted price data
     * Example result - ['value' => "string", 'currency' => "string"]
     *
     * @param float $priceValue
     * @param string $formatCurrency
     * @param bool $isShowPlus
     * @return array
     */
    public function getFormattedPriceData(
        float $priceValue,
        string $formatCurrency,
        bool $isShowPlus = false
    ): array {
        $currency = $this->currencyFactory->create()->load($formatCurrency);
        $value = $currency->format(
            $priceValue,
            ['display' => Currency::NO_SYMBOL],
            false
        );
        if ($value >= 0 && $isShowPlus) {
            $value = '+' . $value;
        }

        return ['value' => $value, 'currency' => $currency->getCode()];
    }
}
