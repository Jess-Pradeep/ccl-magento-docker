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

namespace Aheadworks\CtqGraphQl\Model\Updater\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Api\StoreRepositoryInterface;

class Currency
{
    /**
     * @param CartRepositoryInterface $cartRepository
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly StoreRepositoryInterface $storeRepository
    ) {
    }

    /**
     * Sets cart currency based on specified store.
     *
     * @param Quote $cart
     * @param int $storeId
     * @return CartInterface
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function updateCartCurrency(Quote $cart, int $storeId): CartInterface
    {
        $cartStore = $this->storeRepository->getById($cart->getStoreId());
        $currentCartCurrencyCode = $cartStore->getCurrentCurrency()->getCode();
        if ((int)$cart->getStoreId() !== $storeId) {
            $newStore = $this->storeRepository->getById($storeId);
            if ($cartStore->getWebsite() !== $newStore->getWebsite()) {
                throw new GraphQlInputException(
                    __('Can\'t assign cart to store in different website.')
                );
            }
            $cart->setStoreId($storeId);
            $cart->setStoreCurrencyCode($newStore->getCurrentCurrency());
            $cart->setQuoteCurrencyCode($newStore->getCurrentCurrency());
        } elseif ($cart->getQuoteCurrencyCode() !== $currentCartCurrencyCode) {
            $cart->setQuoteCurrencyCode($cartStore->getCurrentCurrency());
        } else {
            return $cart;
        }
        $this->cartRepository->save($cart);
        $cart = $this->cartRepository->get($cart->getId());

        return $cart;
    }
}
