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
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;

class QuoteInformation extends AbstractEdit
{
    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param QuoteUpdater $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param CartChecker $cartChecker
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        QuoteUpdater $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        private readonly CartChecker $cartChecker,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_quote_information');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText(): Phrase
    {
        return $this->cartChecker->checkIfCustomerAccountMustBeCreated($this->getQuote())
            ? __('Quote Information')
            : __('Quote & Account Information');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass(): string
    {
        return 'head-quote-information';
    }
}
