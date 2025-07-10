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
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Cart;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

class Checker
{
    /**
     * @param BooleanUtils $booleanUtils
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly BooleanUtils $booleanUtils,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Check if customer must be created
     *
     * @param CartInterface $cart
     * @return bool
     */
    public function checkIfCustomerAccountMustBeCreated(CartInterface $cart): bool
    {
        $isGuest = !($cart->getCustomerIsGuest() === null)
            && $this->booleanUtils->toBoolean($cart->getCustomerIsGuest());
        $quoteId = $this->request->getParam(QuoteInterface::ID);

        return (!$quoteId && !$isGuest && !$cart->getCustomerId());
    }
}
