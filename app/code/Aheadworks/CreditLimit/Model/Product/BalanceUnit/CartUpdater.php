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
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\CreditLimit\Model\Currency\RateConverter;

/**
 * Class CartUpdater
 *
 * @package Aheadworks\CreditLimit\Model\Product\BalanceUnit
 */
class CartUpdater
{
    /**
     * @var CartChecker
     */
    private $cartChecker;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var RateConverter
     */
    private $rateConverter;

    /**
     * @param CartChecker $cartChecker
     * @param CartRepositoryInterface $cartRepository
     * @param RateConverter $rateConverter
     */
    public function __construct(
        CartChecker $cartChecker,
        CartRepositoryInterface $cartRepository,
        RateConverter $rateConverter
    ) {
        $this->cartChecker = $cartChecker;
        $this->cartRepository = $cartRepository;
        $this->rateConverter = $rateConverter;
    }

    /**
     * Convert balance unit price on currency change
     *
     * Custom price is not converted automatically by Magento, here is a workaround
     *
     * @param int $cartId
     * @param string $currencyCodeFrom
     * @param string $currencyCodeTo
     * @throws \Exception
     */
    public function convertBalanceUnitPriceOnCurrencyChange($cartId, $currencyCodeFrom, $currencyCodeTo)
    {
        $isProductFound = false;
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $items = $quote->getItems();
        foreach ($items as $item) {
            if ($this->cartChecker->isItemBalanceUnit($item)) {
                $newCustomPrice = $this->rateConverter->convertAmount(
                    $item->getOriginalCustomPrice(),
                    $currencyCodeFrom,
                    $currencyCodeTo
                );
                $item->setCustomPrice($newCustomPrice);
                $item->setOriginalCustomPrice($newCustomPrice);
                $isProductFound = true;
            }
        }

        if ($isProductFound) {
            $this->cartRepository->save($quote);
        }
    }
}
