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
namespace Aheadworks\CreditLimit\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Api\CartManagementInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\Provider as BalanceUnitProvider;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartChecker;

/**
 * Class CartService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CartService implements CartManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var BalanceUnitProvider
     */
    private $balanceUnitProvider;

    /**
     * @var CartChecker
     */
    private $cartChecker;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param BalanceUnitProvider $balanceUnitProvider
     * @param CartChecker $cartChecker
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        BalanceUnitProvider $balanceUnitProvider,
        CartChecker $cartChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->balanceUnitProvider = $balanceUnitProvider;
        $this->cartChecker = $cartChecker;
    }

    /**
     * @inheritdoc
     */
    public function addBalanceUnitToCart($cartId, $price)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $product = $this->balanceUnitProvider->getProduct();
        $items = $quote->getItems();
        foreach ($items as $item) {
            if ($this->cartChecker->isItemBalanceUnit($item)) {
                $quote->removeItem($item->getId());
            }
        }

        $quoteItem = $quote->addProduct($product);
        if (is_string($quoteItem)) {
            throw new LocalizedException(__($quoteItem));
        }
        $quoteItem->setCustomPrice($price);
        $quoteItem->setOriginalCustomPrice($price);
        $quoteItem->setNoDiscount(1);

        $this->cartRepository->save($quote);
    }
}
