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
namespace Aheadworks\CreditLimit\Block\Payment;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Payment\Block\Form as BaseForm;
use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Model\Checkout\Payment;

/**
 * Class Form
 *
 * @package Aheadworks\CreditLimit\Block\Payment
 */
class Form extends BaseForm
{
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var string
     */
    protected $_template = 'Aheadworks_CreditLimit::payment/form.phtml';

    /**
     * @param Context $context
     * @param SessionManagerInterface $session
     * @param PriceCurrencyInterface $priceCurrency
     * @param Payment $payment
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        PriceCurrencyInterface $priceCurrency,
        Payment $payment,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->session = $session;
        $this->payment = $payment;
    }

    /**
     * Get available balance for customer
     *
     * @return float
     */
    public function getCustomerAvailableBalance()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        $amount = $this->payment->getAvailableAmount($quote);
        return $this->priceCurrency->format(
            $amount ?? 0,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $quote->getQuoteCurrencyCode()
        );
    }

    /**
     * Is balance is enough to pay
     *
     * @return bool
     */
    public function isBalanceEnoughToPay()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        return $this->payment->isBalanceEnoughToPay($quote);
    }

    /**
     * Check if credit limit is exceeded
     *
     * @return bool
     */
    public function isCreditLimitExceeded()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        return $this->payment->isCreditLimitExceeded($quote);
    }

    /**
     * Get credit limit exceeded amount
     *
     * @return float
     */
    public function getCreditLimitExceededAmount()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        return $this->priceCurrency->format(
            $this->payment->getCreditLimitExceededAmount($quote),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $quote->getQuoteCurrencyCode()
        );
    }

    /**
     * Get multishipping grand total
     *
     * @return float
     */
    public function getMultishippingTotal()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        return $quote->getGrandTotal();
    }
}
