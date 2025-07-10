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
namespace Aheadworks\Ca\Model\Checkout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\OrderApprovalManagementInterface;


/**
 * Class ConfigProvider
 *
 * @package Aheadworks\Ca\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CONFIG_CODE = 'awCompanyAccounts';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var OrderApprovalManagementInterface
     */
    private $orderApprovalManagement;

    /**
     * @param OrderApprovalManagementInterface $orderApprovalManagement
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     */
    public function __construct(
        OrderApprovalManagementInterface $orderApprovalManagement,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession
    ) {
        $this->orderApprovalManagement = $orderApprovalManagement;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getConfig(): array
    {
        $quote = $this->checkoutSession->getQuote();
        $config = [];
        if ($this->customerSession->isLoggedIn()) {
            $config = [
                self::CONFIG_CODE => [
                    'isNoticeVisible' => $this->orderApprovalManagement->isApproveRequiredForCart($quote->getId())
                ]
            ];
        }

        return $config;
    }
}
