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

namespace Aheadworks\Ctq\Model\QuoteList\Cart;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;

class ConfigProvider extends CompositeConfigProvider
{
    /**
     * @var Quote
     */
    private $_quote;

    /**
     * @param CheckoutSession $session
     * @param Provider $quoteProvider
     * @param State $state
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param Session $customerSession
     * @param array $configProviders
     */
    public function __construct(
        private readonly CheckoutSession $session,
        private readonly Provider $quoteProvider,
        private readonly State $state,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly Session $customerSession,
        array $configProviders
    ) {
        parent::__construct($configProviders);
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $this->_quote = $this->session->getQuote();
        if ($this->quoteProvider->getQuoteId()) {
            $config = $this->state->emulateQuote([$this, 'getParentConfig']);

            return $this->prepareConfig($config);
        }
        return parent::getConfig();
    }

    /**
     * Get parent config
     *
     * @return array
     */
    public function getParentConfig(): array
    {
        return parent::getConfig();
    }

    /**
     * Set quote list flag to checkout config
     *
     * @param array $config
     * @return array
     * @throws LocalizedException
     */
    private function prepareConfig($config)
    {

        $config['isQuoteList'] = true;

        if (!$this->customerSession->isLoggedIn()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $config['quoteData']['entity_id'] = $quoteIdMask->load(
                $this->quoteProvider->getQuoteId(),
                'quote_id'
            )->getMaskedId();
        }

        return $config;
    }
}
