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
namespace Aheadworks\Ctq\Model\QuoteList;

use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Api\Data\CartInterface;

class Checker
{
    /**
     * Check is CTQ Quote
     *
     * @param Quote $quote
     * @return bool
     */
    public function isAwCtqQuote($quote)
    {
        return (bool)$quote->getData(CartInterface::AW_CTQ_IS_QUOTE_LIST);
    }
}
