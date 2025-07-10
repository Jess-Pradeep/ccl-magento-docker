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

namespace Aheadworks\CreditLimit\Model\PaymentReminder\Notifier\VariableProcessor;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;

class CreditBalance implements VariableProcessorInterface
{
    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(private readonly PriceCurrencyInterface $priceCurrency) {
    }

    /**
     * Prepare variables before send
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables): array
    {
        /** @var array $summary */
        $summary = $variables[EmailVariables::SUMMARY];
        $variables[EmailVariables::CREDIT_BALANCE] = $this->prepareCreditBalance($summary);

        return $variables;
    }

    /**
     * Prepare credit balance
     *
     * @param array $summary
     * @return string
     */
    private function prepareCreditBalance(array $summary):string
    {
        $currencySymbol = $this->priceCurrency->getCurrencySymbol(currency: $summary[SummaryInterface::CURRENCY]);

        return $summary[SummaryInterface::CREDIT_BALANCE] . '' . $currencySymbol;
    }
}
