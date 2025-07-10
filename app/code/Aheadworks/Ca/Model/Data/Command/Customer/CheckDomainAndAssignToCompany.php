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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Data\Command\Customer;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Company\Domain\Resolver as DomainResolver;
use Aheadworks\Ca\Model\Company\Domain\Search\Builder as DomainSearchBuilder;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Source\Company\Domain\Status as DomainStatus;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

class CheckDomainAndAssignToCompany implements CommandInterface
{
    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DomainSearchBuilder $domainSearchBuilder
     * @param DomainResolver $domainResolver
     * @param Config $config
     * @param bool $skipUserApprovalCheck
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly DomainSearchBuilder $domainSearchBuilder,
        private readonly DomainResolver $domainResolver,
        private readonly Config $config,
        private readonly bool $skipUserApprovalCheck = false
    ) {
    }

    /**
     * Execute command
     *
     * @param CustomerInterface $customer
     * @return bool
     * @throws LocalizedException
     */
    public function execute($customer): bool
    {
        if (!$customer instanceof CustomerInterface) {
            throw new \InvalidArgumentException(
                sprintf('Provided argument must implement %s', CustomerInterface::class)
            );
        }

        if (!$this->config->isExtensionEnabled((int)$customer->getWebsiteId())
            || ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser())
        ) {
            return false;
        }

        $domainName = $this->domainResolver->resolveFromEmail($customer->getEmail());
        $this->domainSearchBuilder->addNameFilter($domainName);
        $this->domainSearchBuilder->addStatusFilter(DomainStatus::ACTIVE);
        $domainList = $this->domainSearchBuilder->searchDomains();

        if (count($domainList) > 0) {
            $domain = reset($domainList);
            $isApprovalRequired = !$this->skipUserApprovalCheck
                && !$this->config->isUserApprovedAutomatically($customer->getWebsiteId());
            $this->companyUserManagement->assignUserToCompany(
                $customer->getId(),
                $domain->getCompanyId(),
                $isApprovalRequired
            );
            return true;
        }

        return false;
    }
}
