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
namespace Aheadworks\CreditLimit\ViewModel\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\Currency;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class PaymentForm
 *
 * @package Aheadworks\CreditLimit\ViewModel\Customer
 */
class PaymentForm implements ArgumentInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @param ArrayManager $arrayManager
     * @param CustomerManagementInterface $customerManagement
     * @param StoreManagerInterface $storeManager
     * @param CurrencyInterface $currency
     */
    public function __construct(
        ArrayManager $arrayManager,
        CustomerManagementInterface $customerManagement,
        StoreManagerInterface $storeManager,
        CurrencyInterface $currency
    ) {
        $this->arrayManager = $arrayManager;
        $this->customerManagement = $customerManagement;
        $this->storeManager = $storeManager;
        $this->currency = $currency;
    }

    /**
     * Prepare JS layout configuration
     *
     * @param array $jsLayout
     * @param int $customerId
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareJsLayout($jsLayout, $customerId)
    {
        /** @var Currency $storeCurrency */
        $storeCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        $providerPath = $this->arrayManager->findPath('aw_cl_payment_form_provider', $jsLayout);
        if ($providerPath) {
            $providerLayout = $this->arrayManager->get($providerPath, $jsLayout);
            $amount = $this->customerManagement->getCreditBalanceAmount($customerId, $storeCurrency->getCode());
            $amount = $amount < 0 ? abs((float)$amount) : 0;
            $providerLayout['data'] = [
                'amount' => $amount,
                'customer_id' => $customerId
            ];
            $jsLayout = $this->arrayManager->merge($providerPath, $jsLayout, $providerLayout);
        }

        $amountInputFieldPath = $this->arrayManager->findPath('amount_input', $jsLayout);
        if ($amountInputFieldPath) {
            $amountInputFieldLayout = $this->arrayManager->get($amountInputFieldPath, $jsLayout);
            $amountInputFieldLayout['config'] = [
                'addafter' => $storeCurrency->getCurrencySymbol() ?? $storeCurrency->getCurrencyCode()
            ];
            $jsLayout = $this->arrayManager->merge($amountInputFieldPath, $jsLayout, $amountInputFieldLayout);
        }

        return $jsLayout;
    }
}
