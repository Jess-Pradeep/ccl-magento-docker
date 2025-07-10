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
namespace Aheadworks\CreditLimit\Plugin\Tax\Model\Total\Quote;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Magento\Tax\Api\Data\TaxDetailsItemInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartChecker;

/**
 * Class CommonTaxCollectorPlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Tax\Model\Total\Quote
 */
class CommonTaxCollectorPlugin
{
    /**
     * @var TaxHelper
     */
    private $taxHelper;

    /**
     * @var CartChecker
     */
    private $cartChecker;

    /**
     * @param TaxHelper $taxHelper
     * @param CartChecker $cartChecker
     */
    public function __construct(
        TaxHelper $taxHelper,
        CartChecker $cartChecker
    ) {
        $this->taxHelper = $taxHelper;
        $this->cartChecker = $cartChecker;
    }

    /**
     * Update tax related fields for quote item
     *
     * @param CommonTaxCollector $subject
     * @param CommonTaxCollector $result
     * @param AbstractItem $quoteItem
     * @param TaxDetailsItemInterface $itemTaxDetails
     * @return CommonTaxCollector
     */
    public function afterUpdateItemTaxInfo($subject, $result, $quoteItem, $itemTaxDetails)
    {
        if ($this->cartChecker->isItemBalanceUnit($quoteItem)
            && $quoteItem->getCustomPrice()
            && $this->taxHelper->applyTaxOnCustomPrice()
        ) {
            $quoteItem->setCustomPrice($itemTaxDetails->getPrice());
        }

        return $result;
    }
}
