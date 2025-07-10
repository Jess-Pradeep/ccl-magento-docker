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
namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Email\Metadata\Sender;
use Aheadworks\Ca\Model\Email\Metadata\Builder as EmailMetadataBuilder;
use Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList\Provider as RelatedObjectListProvider;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser
 */
class Notifier
{
    /**#@+
     * Notification type
     */
    public const NEW_COMPANY_USER_CREATED = 'new_company_user_created';
    public const NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER
        = 'new_pending_company_user_assigned_for_company_user';
    public const NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN
        = 'new_pending_company_user_assigned_for_company_admin';
    public const NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN = 'new_company_user_assigned_for_company_admin';
    public const NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER = 'new_company_user_assigned_for_company_user';
    public const COMPANY_USER_UNASSIGNED_FOR_COMPANY_USER = 'company_user_unassigned_for_company_user';
    public const COMPANY_USER_UNASSIGNED_FOR_COMPANY_ADMIN = 'company_user_unassigned_for_company_admin';
    public const NEW_COMPANY_ADMIN_ASSIGNED_FOR_COMPANY_ADMIN = 'new_company_admin_assigned_for_company_admin';
    /**#@-*/

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var EmailMetadataBuilder
     */
    private $emailMetadataBuilder;

    /**
     * @var RelatedObjectListProvider
     */
    private $relatedObjectListProvider;

    /**
     * @param Sender $sender
     * @param EmailMetadataBuilder $emailMetadataBuilder
     * @param RelatedObjectListProvider $relatedObjectListProvider
     */
    public function __construct(
        Sender $sender,
        EmailMetadataBuilder $emailMetadataBuilder,
        RelatedObjectListProvider $relatedObjectListProvider
    ) {
        $this->sender = $sender;
        $this->emailMetadataBuilder = $emailMetadataBuilder;
        $this->relatedObjectListProvider = $relatedObjectListProvider;
    }

    /**
     * Notify on company user processes
     *
     * @param CustomerInterface $customer
     * @param string $notificationType
     * @throws LocalizedException
     */
    public function notify($customer, $notificationType)
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            $notificationType,
            $this->relatedObjectListProvider->getByCustomer($customer)
        );

        $this->sender->send($emailMetadata);
    }
}
