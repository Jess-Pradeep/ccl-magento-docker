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
namespace Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor;

use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;

/**
 * Class CustomerName
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor
 */
class CustomerName implements VariableProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var array $customer */
        $customer = $variables[EmailVariables::CUSTOMER];
        $variables[EmailVariables::CUSTOMER_NAME] = $this->prepareCustomerName($customer);

        return $variables;
    }

    /**
     * Prepare customer name
     *
     * @param array $customer
     * @return string
     */
    private function prepareCustomerName($customer)
    {
        return $customer[CustomerInterface::FIRSTNAME] . ' ' . $customer[CustomerInterface::LASTNAME];
    }
}
