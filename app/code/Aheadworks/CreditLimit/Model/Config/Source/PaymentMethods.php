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

namespace Aheadworks\CreditLimit\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\CreditLimit\Model\Checkout\ConfigProvider;

/**
 * Class Payments
 */
class PaymentMethods implements OptionSourceInterface
{
    public const ALL_METHODS_OPTION = 'all';

    /**
     * Payments constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param PaymentConfig $paymentConfig
     */
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private PaymentConfig $paymentConfig
    ) {
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods[self::ALL_METHODS_OPTION] = ['label' => 'All Payment Methods', 'value' => self::ALL_METHODS_OPTION];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->scopeConfig->getValue('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = ['label' => $paymentTitle, 'value' => $paymentCode];
        }
        unset($methods[ConfigProvider::METHOD_CODE]);
        return $methods;
    }
}
