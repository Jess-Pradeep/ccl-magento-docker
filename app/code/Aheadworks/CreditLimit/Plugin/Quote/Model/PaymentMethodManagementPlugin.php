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

namespace Aheadworks\CreditLimit\Plugin\Quote\Model;

use Magento\Quote\Api\PaymentMethodManagementInterface as Subject;
use Magento\Quote\Api\Data\PaymentMethodInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Model\Config\Source\PaymentMethods as SourcePaymentMethods;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartChecker;

/**
 * Class PaymentMethodManagementPlugin
 */
class PaymentMethodManagementPlugin
{
    public function __construct(
        private CheckoutSession $checkoutSession,
        private Config $config,
        private CartChecker $cartChecker
    ) {
    }

    /**
     * Show payment methods according to module setting
     *
     * @param Subject $subject
     * @param PaymentMethodInterface[] $paymentMethods
     * @param int $cartId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetList(Subject $subject, array $paymentMethods, int $cartId): array
    {
        $quote = $this->checkoutSession->getQuote();
        if ($quote && (int)$quote->getId() === $cartId && $this->cartChecker->isBalanceUnitFoundInQuote($quote)) {
            $allowPaymentMethods = $this->config->getAllowPaymentMethods($quote->getStore()->getWebsiteId());
            if (!$allowPaymentMethods) {
                return [];
            }
            $allowPaymentMethods = explode(',', $allowPaymentMethods);
            if (is_array($allowPaymentMethods) &&
                !in_array(SourcePaymentMethods::ALL_METHODS_OPTION, $allowPaymentMethods)) {
                foreach ($paymentMethods as $methodKey => $paymentMethod) {
                    if (!in_array($paymentMethod->getCode(), $allowPaymentMethods)) {
                        unset($paymentMethods[$methodKey]);
                    }
                }
            }
        }
        return $paymentMethods;
    }
}
