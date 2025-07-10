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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\Ca\Model\Customer\Checker\CustomerStatus;
use Aheadworks\Ca\Model\Quote\Permission\Checker\Company as QuotePermissionChecker;

/**
 * Class SellerQuoteManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class SellerQuoteManagementPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CustomerStatus
     */
    private $customerStatus;

    /**
     * @var QuotePermissionChecker
     */
    private $quotePermissionChecker;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param CustomerStatus $customerStatus
     * @param QuotePermissionChecker $quotePermissionChecker
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CustomerStatus $customerStatus,
        QuotePermissionChecker $quotePermissionChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->customerStatus = $customerStatus;
        $this->quotePermissionChecker = $quotePermissionChecker;
    }

    /**
     * Check if admin is able to create a quote for specific customer
     *
     * @param \Aheadworks\Ctq\Api\SellerQuoteManagementInterface $subject
     * @param int $cartId
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return null
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreateQuote($subject, $cartId, $quote)
    {
        $cart = $this->cartRepository->get($cartId);
        $customerId = $cart->getCustomer()->getId();
        if ($customerId) {
            $this->customerStatus->checkAndEnsureCustomerStatusIsValid($customerId);
            if (!$this->quotePermissionChecker->check($customerId, $quote->getStoreId())) {
                throw new LocalizedException(__('Quote is disabled for company.'));
            }
        }

        return null;
    }

    /**
     * Check if admin is able to update a quote for specific customer
     *
     * @param \Aheadworks\Ctq\Api\SellerQuoteManagementInterface $subject
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return null
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeUpdateQuote($subject, $quote)
    {
        $customerId = $quote->getCustomerId();
        if ($customerId) {
            $this->customerStatus->checkAndEnsureCustomerStatusIsValid($customerId);
            if (!$this->quotePermissionChecker->check($customerId, $quote->getStoreId())) {
                throw new LocalizedException(__('Quote is disabled for company.'));
            }
        }

        return null;
    }
}
