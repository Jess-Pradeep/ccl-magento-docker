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
namespace Aheadworks\Ca\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Config
{
    /**#@+
     * Constants for config path
     */
    public const XML_PATH_GENERAL_IS_ENABLED = 'aw_ca/general/is_enabled';
    public const XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE = 'aw_ca/general/default_sales_representative';
    public const XML_PATH_GENERAL_ENABLED_ORDER_APPROVAL = 'aw_ca/general/enabled_order_approval';
    public const XML_PATH_GENERAL_ENABLED_REGISTRATION_FOR = 'aw_ca/general/enabled_registration_for';
    public const XML_PATH_GENERAL_IS_USER_APPROVED_AUTOMATICALLY = 'aw_ca/general/is_user_approved_automatically';
    public const XML_PATH_GENERAL_HEAD_UNIT_TITLE = 'aw_ca/general/head_unit_title';
    public const XML_PATH_GENERAL_HEAD_UNIT_DESCRIPTION = 'aw_ca/general/head_unit_description';
    public const XML_PATH_EMAIL_SENDER = 'aw_ca/email/sender';
    public const XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE = 'aw_ca/email/new_company_approved_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE = 'aw_ca/email/new_company_submitted_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE = 'aw_ca/email/new_company_declined_template';
    public const XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE = 'aw_ca/email/company_status_changed_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE = 'aw_ca/email/new_company_user_created_template';
    public const XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE
        = 'aw_ca/email/new_pending_company_user_assigned_for_company_user_template';
    public const XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/new_pending_company_user_assigned_for_company_admin_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/new_company_user_assigned_for_company_admin_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE
        = 'aw_ca/email/new_company_user_assigned_for_company_user_template';
    public const XML_PATH_EMAIL_COMPANY_USER_UNASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/company_user_unassigned_for_company_admin_template';
    public const XML_PATH_EMAIL_COMPANY_USER_UNASSIGNED_FOR_COMPANY_USER_TEMPLATE
        = 'aw_ca/email/company_user_unassigned_for_company_user_template';
    public const XML_PATH_EMAIL_NEW_COMPANY_ADMIN_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/new_company_admin_assigned_for_company_admin_template';
    public const XML_PATH_EMAIL_NEW_ADMIN_CHANGE_REQUEST_BY_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/new_admin_change_request_by_company_admin_template';
    public const XML_PATH_EMAIL_ADMIN_CHANGE_REQUEST_DECLINED_BY_BACKEND_ADMIN_TEMPLATE
        = 'aw_ca/email/admin_change_request_declined_by_backend_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_CREATED_BY_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_created_by_company_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_APPROVED_BY_BACKEND_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_approved_by_backend_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_BACKEND_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_status_changed_by_backend_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_status_changed_by_company_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_DELETED_BY_BACKEND_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_deleted_by_backend_admin_template';
    public const XML_PATH_EMAIL_DOMAIN_DELETED_BY_COMPANY_ADMIN_TEMPLATE
        = 'aw_ca/email/domain_deleted_by_company_admin_template';
    public const XML_PATH_EMAIL_ORDER_WAS_SENT_FOR_APPROVAL_TEMPLATE
        = 'aw_ca/email/order_was_sent_for_approval_template';
    public const XML_PATH_EMAIL_ORDER_STATUS_CHANGED_TEMPLATE = 'aw_ca/email/order_status_changed_template';
    public const XML_PATH_COMPANY_FORM_CUSTOMIZATION_FIELDSET = 'aw_ca/company_form_customization/fieldset_';
    public const XML_PATH_HISTORY_LOG_ENABLED_HISTORY_LOG = 'aw_ca/history_log/enabled_history_log';
    public const XML_PATH_HISTORY_LOG_LIFETIME = 'aw_ca/history_log/lifetime';
    public const XML_PATH_HISTORY_LOG_FREQUENCY_LOG_CLEANING = 'aw_ca/history_log/frequency_log_cleaning';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Check if extension is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isExtensionEnabled(?int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve backend user ID as sales representative
     *
     * @param int|null $websiteId
     * @return int|null
     */
    public function getDefaultSalesRepresentative($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if order approval is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isOrderApprovalEnabled($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLED_ORDER_APPROVAL,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if registration on frontend is enabled
     *
     * @param string $type
     * @param int|null $websiteId
     * @return bool
     */
    public function isRegistrationOnFrontendEnabled($type, $websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLED_REGISTRATION_FOR,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $allowedRegistrationTypes = explode(',', (string)$value);
        return $value !== null && in_array($type, $allowedRegistrationTypes);
    }

    /**
     * Check if user is approved automatically
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isUserApprovedAutomatically($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_USER_APPROVED_AUTOMATICALLY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Default Head unit title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getHeadUnitTitle($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_HEAD_UNIT_TITLE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Default Head unit description
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getHeadUnitDescription($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_HEAD_UNIT_DESCRIPTION,
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
     * Retrieve new company approved email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyApprovedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company submitted email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanySubmittedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company declined email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyDeclinedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company status changed email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyStatusChangedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company user created email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyUserCreatedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new pending company user assigned, for company user template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewPendingCompanyUserAssignedForCompanyUserTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new pending company user assigned, for company admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewPendingCompanyUserAssignedForCompanyAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company user assigned, for company admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyUserAssignedForCompanyAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company user assigned, for company user template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyUserAssignedForCompanyUserTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company domain created by company admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyDomainCreatedByCompanyAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_CREATED_BY_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company domain approved by backend admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyDomainApprovedByBackendAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_APPROVED_BY_BACKEND_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company domain status changed by backend admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyDomainStatusChangedByBackendAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_BACKEND_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company domain status changed by company admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyDomainStatusChangedByCompanyAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company domain deleted by backend admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyDomainDeletedByBackendAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_DELETED_BY_BACKEND_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company domain deleted by company admin email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyDomainDeletedByCompanyAdminTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_DOMAIN_DELETED_BY_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve order was sent for approval email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOrderWasSentForApprovalTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ORDER_WAS_SENT_FOR_APPROVAL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve order status changed email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOrderStatusChangedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ORDER_STATUS_CHANGED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve fieldset fields customization
     *
     * @param string $fieldsetName
     * @param int|null $storeId
     * @return array
     */
    public function getFieldsetFieldsCustomization($fieldsetName, $storeId)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_COMPANY_FORM_CUSTOMIZATION_FIELDSET . $fieldsetName,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Check if history log is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isHistoryLogEnabled(?int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HISTORY_LOG_ENABLED_HISTORY_LOG,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve Lifetime history log
     *
     * @param int|null $websiteId
     * @return int|null
     */
    public function getLifetime(?int $websiteId = null): ?int
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HISTORY_LOG_LIFETIME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve Frequency Log Cleaning
     *
     * @param int|null $websiteId
     * @return int|null
     */
    public function getFrequencyLogCleaning(?int $websiteId = null): ?int
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HISTORY_LOG_FREQUENCY_LOG_CLEANING,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve company user unassigned from company, admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyUserUnassignedForCompanyAdminTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_COMPANY_USER_UNASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company user unassigned from company, user template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyUserUnassignedForCompanyUserTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_COMPANY_USER_UNASSIGNED_FOR_COMPANY_USER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company admin assigned to company, company admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyAdminAssignedForCompanyAdminTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_ADMIN_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new change request by company admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewChangeRequestByCompanyAdminTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_ADMIN_CHANGE_REQUEST_BY_COMPANY_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve change request declined by backend admin template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getChangeRequestDeclinedByBackendAdminTemplate(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ADMIN_CHANGE_REQUEST_DECLINED_BY_BACKEND_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
