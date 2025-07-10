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
namespace Aheadworks\Ca\Model\Company\Domain;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Email\Metadata\Sender;
use Aheadworks\Ca\Model\Email\Metadata\Builder as EmailMetadataBuilder;
use Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList\Provider as RelatedObjectListProvider;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ca\Model\Company\Domain
 */
class Notifier
{
    /**#@+
     * Notification types
     */
    const DOMAIN_CREATED_BY_COMPANY_ADMIN = 'domain_created_by_company_admin';
    const DOMAIN_APPROVED_BY_BACKEND_ADMIN = 'domain_approved_by_backend_admin';
    const DOMAIN_STATUS_CHANGED_BY_BACKEND_ADMIN = 'domain_status_changed_by_backend_admin';
    const DOMAIN_STATUS_CHANGED_BY_COMPANY_ADMIN = 'domain_status_changed_by_company_admin';
    const DOMAIN_DELETED_BY_BACKEND_ADMIN = 'domain_deleted_by_backend_admin';
    const DOMAIN_DELETED_BY_COMPANY_ADMIN = 'domain_deleted_by_company_admin';
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
     * Notify about new created domain
     *
     * @param CompanyDomainInterface $domain
     * @throws LocalizedException
     */
    public function notifyOnNewDomainCreated($domain)
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            self::DOMAIN_CREATED_BY_COMPANY_ADMIN,
            $this->relatedObjectListProvider->getByDomain($domain)
        );

        $this->sender->send($emailMetadata);
    }

    /**
     * Notify about domain approval by sales representative
     *
     * @param CompanyDomainInterface $domain
     * @throws LocalizedException
     */
    public function notifyOnDomainApproved($domain)
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            self::DOMAIN_APPROVED_BY_BACKEND_ADMIN,
            $this->relatedObjectListProvider->getByDomain($domain)
        );

        $this->sender->send($emailMetadata);
    }

    /**
     * Notify about domain status changed
     *
     * @param CompanyDomainInterface $domain
     * @param CompanyDomainInterface $oldDomain
     * @param string $requestedBy
     * @throws LocalizedException
     */
    public function notifyOnStatusChanged($domain, $oldDomain, $requestedBy)
    {
        $relatedObjectList = $this->relatedObjectListProvider->getByDomain($domain);
        $relatedObjectList[ModifierInterface::OLD_DOMAIN] = $oldDomain;
        $emailMetadata = $this->emailMetadataBuilder->build(
            'domain_status_changed_by_' . $requestedBy,
            $relatedObjectList
        );

        $this->sender->send($emailMetadata);
    }

    /**
     * Notify about deleted domain
     *
     * @param CompanyDomainInterface $domain
     * @param string $requestedBy
     * @throws LocalizedException
     */
    public function notifyOnDelete($domain, $requestedBy)
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            'domain_deleted_by_' . $requestedBy,
            $relatedObjectList = $this->relatedObjectListProvider->getByDomain($domain)
        );

        $this->sender->send($emailMetadata);
    }
}
