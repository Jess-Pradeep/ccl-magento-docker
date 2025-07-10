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
namespace Aheadworks\CreditLimit\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\CreditLimit\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     */
    const METHOD_CODE = 'aw_credit_limit';

    /**
     * ConfigProvider constructor.
     *
     * @param CustomerManagementInterface $customerManagement
     * @param CheckoutSession $session
     * @param PaymentHelper $paymentHelper
     * @param StoreManagerInterface $storeManager
     * @param PaymentPeriodManagementInterface $paymentPeriodService
     */
    public function __construct(
        private CustomerManagementInterface $customerManagement,
        private CheckoutSession $session,
        private PaymentHelper $paymentHelper,
        private StoreManagerInterface $storeManager,
        private PaymentPeriodManagementInterface $paymentPeriodService
    ) {
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getConfig(): array
    {
        $paymentMethod = $this->paymentHelper->getMethodInstance(self::METHOD_CODE);
        $quote = $this->session->getQuote();
        return $paymentMethod->isAvailable($quote) ? [
            'payment' => [
                self::METHOD_CODE => $this->getPaymentData($quote)
            ]
        ] : [];
    }

    /**
     * Get payment data
     *
     * @param Quote $quote
     * @throws NoSuchEntityException
     * @return array
     */
    private function getPaymentData($quote)
    {
        $store = $this->storeManager->getStore($quote->getStoreId());

        return [
            SummaryInterface::CREDIT_AVAILABLE => $this->customerManagement->getCreditAvailableAmount(
                $quote->getCustomerId(),
                $store->getCurrentCurrency()->getCode()
            ),
            SummaryInterface::IS_ALLOWED_TO_EXCEED => $this->customerManagement->isAllowedToExceedCreditLimit(
                $quote->getCustomerId()
            ),
            SummaryInterface::PAYMENT_PERIOD => $this->getPaymentPeriod((int)$quote->getCustomerId()),
            'is_payment_period_expired' => !$this->paymentPeriodService->isPlaceOrderAvailable(
                (int)$quote->getCustomerId()
            )
        ];
    }

    /**
     * Get payment period if due date didn't set
     *
     * @param int $customerId
     * @return int|null
     */
    private function getPaymentPeriod(int $customerId): ?int
    {
        $result = null;
        if (!$this->paymentPeriodService->getDueDate($customerId)) {
            $result = $this->paymentPeriodService->getPaymentPeriod($customerId);
        }
        return $result;
    }
}
