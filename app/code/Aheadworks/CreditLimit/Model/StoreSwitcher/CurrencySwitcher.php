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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\StoreSwitcher;

use Magento\Store\Model\StoreSwitcherInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartUpdater;

/**
 * Switch currency by store
 */
class CurrencySwitcher implements StoreSwitcherInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartUpdater
     */
    private $cartUpdater;

    /**
     * CurrencySwitcher constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param CartUpdater $cartUpdater
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartUpdater $cartUpdater
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartUpdater = $cartUpdater;
    }

    /**
     * Switch currency cart item by store
     *
     * @param StoreInterface $fromStore store where we came from
     * @param StoreInterface $targetStore store where to go to
     * @param string $redirectUrl original url requested for redirect after switching
     * @return string redirect url
     * @throws \Exception
     */
    public function switch(StoreInterface $fromStore, StoreInterface $targetStore, string $redirectUrl): string
    {
        $quote = $this->checkoutSession->getQuote();
        $currencyCodeFrom = $fromStore->getCurrentCurrencyCode();
        $currencyCodeTo = $targetStore->getCurrentCurrencyCode();
        if ($quote->getId()) {
            $this->cartUpdater->convertBalanceUnitPriceOnCurrencyChange(
                $quote->getId(),
                $currencyCodeFrom,
                $currencyCodeTo
            );
        }
        return $redirectUrl;
    }
}
