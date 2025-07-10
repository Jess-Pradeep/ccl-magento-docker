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
namespace Aheadworks\CreditLimit\Plugin\Directory\Controller\Currency;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Controller\Currency\SwitchAction;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartUpdater;

/**
 * Class SwitchActionPlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Directory\Controller\Currency
 */
class SwitchActionPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CartUpdater
     */
    private $cartUpdater;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CartUpdater $cartUpdater
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartUpdater $cartUpdater,
        StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartUpdater = $cartUpdater;
        $this->storeManager = $storeManager;
    }

    /**
     * Convert balance unit price on currency change
     *
     * @param SwitchAction $subject
     * @param callable $proceed
     * @return null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundExecute($subject, callable $proceed)
    {
        $quote = $this->checkoutSession->getQuote();
        $currencyCodeFrom = $quote->getQuoteCurrencyCode();
        $result = $proceed();
        $currencyCodeTo = $this->storeManager->getStore()->getCurrentCurrencyCode();
        if ($quote->getId()) {
            $this->cartUpdater->convertBalanceUnitPriceOnCurrencyChange(
                $quote->getId(),
                $currencyCodeFrom,
                $currencyCodeTo
            );
        }

        return $result;
    }
}
