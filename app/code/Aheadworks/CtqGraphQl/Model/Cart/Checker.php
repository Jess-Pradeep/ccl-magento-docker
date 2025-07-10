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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\Cart;

use Aheadworks\Ctq\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class Checker
{
    /**
     * Check if quote list
     *
     * @param Quote $cart
     * @return bool
     */
    public function isQuoteList(Quote $cart): bool
    {
        return (bool)$cart->getData(CartInterface::AW_CTQ_IS_QUOTE_LIST);
    }
}
