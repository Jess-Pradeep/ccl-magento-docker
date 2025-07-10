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
 * @package    CreditLimitGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimitGraphQl\Plugin\QuoteGraphQl\Model\Cart;

use Aheadworks\CreditLimit\Model\Product\BalanceUnitInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Cart\AddProductsToCart as Subject;

/**
 * Class AddProductsToCartPlugin to control balance unit product in cart
 */
class AddProductsToCartPlugin
{
    /**
     * Checking credit limit balance unit product cart item
     *
     * @param Subject $subject
     * @param Quote $cart
     * @param array $cartItems
     * @return array
     * @throws GraphQlInputException
     */
    public function beforeExecute(Subject $subject, Quote $cart, array $cartItems): array
    {
        foreach ($cartItems as $cartData) {
            if ($cartData['data']['sku'] === BalanceUnitInterface::SKU && !isset($cartData['data']['aw_cl_amount'])) {
                throw new GraphQlInputException(
                    __('Missed aw_cl_amount field in cart item data for %1 product', BalanceUnitInterface::SKU)
                );
            }
        }

        return [$cart, $cartItems];
    }
}
