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
namespace Aheadworks\CreditLimit\Model\Product\BalanceUnit;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Aheadworks\CreditLimit\Model\Product\BalanceUnitInterface;

/**
 * Class CartChecker
 *
 * @package Aheadworks\CreditLimit\Model\Product\BalanceUnit
 */
class CartChecker
{
    /**
     * Check if item represents balance unit
     *
     * @param  AbstractItem|QuoteItem|InvoiceItem $item
     * @return bool
     */
    public function isItemBalanceUnit($item)
    {
        return $item->getSku() == BalanceUnitInterface::SKU;
    }

    /**
     * Check if quote contains balance unit product
     *
     * @param Quote $quote
     * @return bool
     */
    public function isBalanceUnitFoundInQuote($quote)
    {
        $result = false;
        $items = $quote->getItems();
        if (!is_array($items)) {
            return $result;
        }
        foreach ($items as $item) {
            if ($this->isItemBalanceUnit($item)) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
