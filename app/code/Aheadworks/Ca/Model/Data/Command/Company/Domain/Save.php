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
namespace Aheadworks\Ca\Model\Data\Command\Company\Domain;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\CompanyDomainManagementInterface;
use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Ca\Model\Data\Command\Company\Domain
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CompanyDomainManagementInterface
     */
    private $companyDomainManagement;

    /**
     * @var CompanyDomainRepositoryInterface
     */
    private $companyDomainRepository;

    /**
     * @var CompanyDomainInterfaceFactory
     */
    private $companyDomainFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CompanyDomainManagementInterface $companyDomainManagement
     * @param CompanyDomainRepositoryInterface $companyDomainRepository
     * @param CompanyDomainInterfaceFactory $companyDomainFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CompanyDomainManagementInterface $companyDomainManagement,
        CompanyDomainRepositoryInterface $companyDomainRepository,
        CompanyDomainInterfaceFactory $companyDomainFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->companyDomainManagement = $companyDomainManagement;
        $this->companyDomainRepository = $companyDomainRepository;
        $this->companyDomainFactory = $companyDomainFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($domainData)
    {
        if (!isset($domainData[CompanyDomainInterface::REQUESTED_BY])) {
            throw new \InvalidArgumentException('requested_by argument is required');
        }

        if (isset($domainData[CompanyDomainInterface::ID]) && !empty($domainData[CompanyDomainInterface::ID])) {
            $companyDomain = $this->companyDomainRepository->get($domainData[CompanyDomainInterface::ID]);
            $this->dataObjectHelper->populateWithArray(
                $companyDomain,
                $domainData,
                CompanyDomainInterface::class
            );

            return $this->companyDomainManagement->updateDomain(
                $companyDomain,
                $domainData[CompanyDomainInterface::REQUESTED_BY]
            );
        }

        /** @var CompanyDomainInterface $companyDomain */
        $companyDomain = $this->companyDomainFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $companyDomain,
            $domainData,
            CompanyDomainInterface::class
        );

        return $this->companyDomainManagement->createDomain(
            $companyDomain,
            $domainData[CompanyDomainInterface::REQUESTED_BY]
        );
    }
}
