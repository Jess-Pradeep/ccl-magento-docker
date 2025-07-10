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

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Template\Context;
use Aheadworks\Ctq\Model\Cart\Checker as CartChecker;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Model\Quote\Address\ShippingInfoChecker;

class ShippingInformation extends AbstractEdit
{
    /**
     * @param Context $context
     * @param QuoteSession $sessionQuote
     * @param QuoteUpdater $quoteUpdater
     * @param PriceCurrencyInterface $priceCurrency
     * @param ShippingInfoChecker $shippingInfoChecker
     * @param CartChecker $cartChecker
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteSession $sessionQuote,
        QuoteUpdater $quoteUpdater,
        PriceCurrencyInterface $priceCurrency,
        private readonly ShippingInfoChecker $shippingInfoChecker,
        private readonly CartChecker $cartChecker,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $quoteUpdater, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_shipping_information');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText(): Phrase
    {
        return __('Shipping Information');
    }

    /**
     * Check if address is specified by customer
     *
     * @return bool
     */
    public function isShippingInfoSpecified(): bool
    {
        return $this->shippingInfoChecker->isInfoSpecified($this->getQuote())
            || $this->checkIfCustomerAccountMustBeCreated();
    }

    /**
     * Check if customer account must be created
     *
     * @return bool
     */
    public function checkIfCustomerAccountMustBeCreated(): bool
    {
        return $this->cartChecker->checkIfCustomerAccountMustBeCreated($this->quoteUpdater->getQuote());
    }
}
