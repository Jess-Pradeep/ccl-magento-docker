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
namespace Aheadworks\Ca\Model\Company\Admin;

use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Email\Metadata\Sender;
use Aheadworks\Ca\Model\Email\Metadata\Builder as EmailMetadataBuilder;
use Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList\Provider as RelatedObjectListProvider;

class Notifier
{
    public const NEW_ADMIN_CHANGE_REQUEST_BY_COMPANY_ADMIN = 'new_admin_change_request_by_company_admin';
    public const ADMIN_CHANGE_REQUEST_DECLINED_BY_BACKEND_ADMIN = 'admin_change_request_declined_by_backend_admin';

    /**
     * @param Sender $sender
     * @param EmailMetadataBuilder $emailMetadataBuilder
     * @param RelatedObjectListProvider $relatedObjectListProvider
     */
    public function __construct(
        private readonly Sender $sender,
        private readonly EmailMetadataBuilder $emailMetadataBuilder,
        private readonly RelatedObjectListProvider $relatedObjectListProvider
    ) {
    }

    /**
     * Notify about new created candidate
     *
     * @param CompanyAdminCandidateInterface $candidate
     * @return void
     * @throws LocalizedException
     */
    public function notifyOnCreate(CompanyAdminCandidateInterface $candidate): void
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            self::NEW_ADMIN_CHANGE_REQUEST_BY_COMPANY_ADMIN,
            $this->relatedObjectListProvider->getByCandidate($candidate)
        );

        $this->sender->send($emailMetadata);
    }

    /**
     * Notify about declined candidate
     *
     * @param CompanyAdminCandidateInterface $candidate
     * @return void
     * @throws LocalizedException
     */
    public function notifyOnDecline(CompanyAdminCandidateInterface $candidate): void
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            self::ADMIN_CHANGE_REQUEST_DECLINED_BY_BACKEND_ADMIN,
            $this->relatedObjectListProvider->getByCandidate($candidate)
        );

        $this->sender->send($emailMetadata);
    }
}
