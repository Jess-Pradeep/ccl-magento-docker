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
use Magento\QuoteGraphQl\Model\Cart\AddSimpleProductToCart as Subject;
use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Model\Data\Command\Balance\MakePayment;

/**
 * Class AddSimpleProductToCartPlugin to control balance unit product in cart
 */
class AddSimpleProductToCartPlugin
{
    /**
     * AddSimpleProductToCartPlugin constructor.
     *
     * @param MakePayment $makePaymentCommand
     */
    public function __construct(
        private MakePayment $makePaymentCommand
    ) {
    }

    /**
     * Correct adding credit limit balance unit product to cart
     *
     * @param Subject $subject
     * @param callable $proceed
     * @param Quote $cart
     * @param array $cartItemData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(Subject $subject, callable $proceed, Quote $cart, array $cartItemData): void
    {
        if ($cartItemData['data']['sku'] === BalanceUnitInterface::SKU) {
            $this->makePaymentCommand->execute(
                [
                    'quote' => $cart,
                    'amount' => $cartItemData['data']['aw_cl_amount']
                ]
            );
        } else {
            $proceed($cart, $cartItemData);
        }
    }
}
