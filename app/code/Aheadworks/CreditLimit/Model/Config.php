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
namespace Aheadworks\CreditLimit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_EMAIL_SENDER = 'aw_credit_limit/email_settings/sender';
    const XML_PATH_GENERAL_IS_ALLOWED_TO_UPDATE_CREDIT_BALANCE
        = 'aw_credit_limit/general/is_allowed_to_update_credit_balance';
    const XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE
        = 'aw_credit_limit/email_settings/can_send_email_on_balance_update';
    const XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE
        = 'aw_credit_limit/email_settings/credit_balance_updated_template';
    const XML_PATH_EMAIL_SEND_REMINDER_PAYMENT_EMAIL_EVERY_X_DAYS
        = 'aw_credit_limit/email_settings/send_email_on_balance_negative_every_x_days';
    const XML_PATH_EMAIL_CREDIT_BALANCE_REMINDER_TEMPLATE
        = 'aw_credit_limit/email_settings/credit_balance_reminder_template';
    const XML_PATH_GENERAL_IS_ENABLE_AUTO_GENERATE_INVOICE = 'aw_credit_limit/general/is_enable_auto_generate_invoice';
    const XML_PATH_GENERAL_ALLOW_PAYMENT_METHODS = 'aw_credit_limit/general/allow_payment_methods';
    /**#@-*/

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Check if it's allowed to update credit balance
     *
     * @param int|null $websiteId
     * @return string
     */
    public function isAllowedToUpdateCreditBalance($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_ALLOWED_TO_UPDATE_CREDIT_BALANCE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve email sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderName($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderEmail($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if allowed to send email on balance update
     *
     * @param int|null $storeId
     * @return string
     */
    public function isAllowedToSendEmailOnBalanceUpdate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve credit balance updated email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCreditBalanceUpdatedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get send reminder every x days after credit balance is negative
     *
     * @param int|null $websiteId
     * @return int|null
     */
    public function getSendPaymentReminderDays(?int $websiteId = null): ?int
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SEND_REMINDER_PAYMENT_EMAIL_EVERY_X_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve credit balance reminder email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCreditBalanceReminderTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CREDIT_BALANCE_REMINDER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is enable auto generate invoice
     *
     * @param int|null $storeId
     * @return string
     */
    public function isEnableAutoGenerateInvoice($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_ENABLE_AUTO_GENERATE_INVOICE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get allow payment methods
     *
     * @param int|string|null $websiteId
     * @return string|array
     */
    public function getAllowPaymentMethods($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ALLOW_PAYMENT_METHODS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
