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
namespace Aheadworks\Ctq\ViewModel\Checkout;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Model\Config;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\ViewModel\Checkout
 */
class RequestQuoteLink implements ArgumentInterface
{
    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var BuyerPermissionManagementInterface
     */
    protected $buyerPermissionManagement;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param CustomerUrl $customerUrl
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param Config $config
     */
    public function __construct(
        CustomerUrl $customerUrl,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        Config $config
    ) {
        $this->customerUrl = $customerUrl;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
        $this->config = $config;
    }

    /**
     * Retrieve url for Request a Quote button
     *
     * @return string
     */
    public function getSignInUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * Check if Request a Quote functionality is available
     *
     * @return bool
     */
    public function isRequestQuoteAvailable()
    {
        return $this->config->isQuoteRequestAllowedFromCart();
    }

    /**
     * Check if Request a Quote button is available
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isRequestQuoteButtonAvailable()
    {
        $cartId = $this->checkoutSession->getQuote()->getId();
        return $this->buyerPermissionManagement->canShowRequestQuoteButton($cartId)
            && $this->config->isQuoteRequestAllowedFromCart();
    }

    /**
     * Check is customer is authenticate
     *
     * @return bool
     */
    public function isAuthenticatedCustomer()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get current quote id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteId()
    {
        return $this->checkoutSession->getQuote()->getId();
    }
}
