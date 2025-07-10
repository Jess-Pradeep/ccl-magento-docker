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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Model\CurrencyFactory;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;

/**
 * Quote Currency Selector
 */
class QuoteCurrency extends AbstractEdit
{
    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @param Context $context
     * @param QuoteSession $sessionQuote
     * @param QuoteUpdater $quoteUpdater
     * @param PriceCurrencyInterface $priceCurrency
     * @param CurrencyFactory $currencyFactory
     * @param CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteSession $sessionQuote,
        QuoteUpdater $quoteUpdater,
        PriceCurrencyInterface $priceCurrency,
        CurrencyFactory $currencyFactory,
        CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context, $sessionQuote, $quoteUpdater, $priceCurrency, $data);
    }

    /**
     * Is quote currency selector is visible
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isVisible()
    {
        return $this->getQuoteViewModel()->isEditQuote($this->getQuoteId())
            && count($this->getAvailableCurrencies()) > 1;
    }

    /**
     * Retrieve available currency codes
     *
     * @return string[]
     * @throws NoSuchEntityException
     */
    public function getAvailableCurrencies()
    {
        $dirtyCodes = $this->getStore()->getAvailableCurrencyCodes(true);
        $codes = [];
        if (is_array($dirtyCodes) && count($dirtyCodes)) {
            $rates = $this->currencyFactory->create()->getCurrencyRates(
                $this->_storeManager->getStore()->getBaseCurrency(),
                $dirtyCodes
            );
            foreach ($dirtyCodes as $code) {
                if (isset($rates[$code]) || $code == $this->_storeManager->getStore()->getBaseCurrencyCode()) {
                    $codes[] = $code;
                }
            }
        }
        return $codes;
    }

    /**
     * Retrieve currency name by code
     *
     * @param string $code
     * @return string
     */
    public function getCurrencyName($code)
    {
        return $this->localeCurrency->getCurrency($code)->getName();
    }

    /**
     * Retrieve currency name by code
     *
     * @param string $code
     * @return string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->localeCurrency->getCurrency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrencyCode();
    }
}
