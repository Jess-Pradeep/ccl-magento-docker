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
namespace Aheadworks\Ca\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyDomainManagementInterface;
use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Company\Domain\Notifier;
use Aheadworks\Ca\Model\Source\Company\Domain\Status as DomainStatus;
use Aheadworks\Ca\Model\Source\Company\Domain\AdminType;

/**
 * Class CompanyDomainService
 *
 * @package Aheadworks\Ca\Model\Service
 */
class CompanyDomainService implements CompanyDomainManagementInterface
{
    /**
     * @var CompanyDomainRepositoryInterface
     */
    private $companyDomainRepository;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param CompanyDomainRepositoryInterface $companyDomainRepository
     * @param Notifier $notifier
     */
    public function __construct(
        CompanyDomainRepositoryInterface $companyDomainRepository,
        Notifier $notifier
    ) {
        $this->companyDomainRepository = $companyDomainRepository;
        $this->notifier = $notifier;
    }

    /**
     * @inheritdoc
     */
    public function createDomain($domain, $requestedBy)
    {
        $domain = $this->companyDomainRepository->save($domain);
        if ($requestedBy == AdminType::COMPANY_ADMIN) {
            $this->notifier->notifyOnNewDomainCreated($domain);
        } else {
            $this->notifyOnDomainApproved($domain);
        }

        return $domain;
    }

    /**
     * @inheritdoc
     */
    public function updateDomain($domain, $requestedBy)
    {
        $oldDomain = $this->companyDomainRepository->get($domain->getId(), true);
        $this->companyDomainRepository->save($domain);
        if (!$domain->getIsApprovedNotificationSent()) {
            $this->notifyOnDomainApproved($domain);
        } elseif ($domain->getStatus() != $oldDomain->getStatus()) {
            $this->notifier->notifyOnStatusChanged($domain, $oldDomain, $requestedBy);
        }

        return $domain;
    }

    /**
     * @inheritdoc
     */
    public function deleteDomain($domain, $requestedBy)
    {
        $this->companyDomainRepository->delete($domain);
        $this->notifier->notifyOnDelete($domain, $requestedBy);

        return true;
    }

    /**
     * Notify on domain approved
     *
     * @param CompanyDomainInterface $domain
     * @throws LocalizedException
     */
    private function notifyOnDomainApproved($domain)
    {
        if ($domain->getStatus() == DomainStatus::ACTIVE && !$domain->getIsApprovedNotificationSent()) {
            $this->notifier->notifyOnDomainApproved($domain);
            $domain->setIsApprovedNotificationSent(true);
            $this->companyDomainRepository->save($domain);
        }
    }
}
