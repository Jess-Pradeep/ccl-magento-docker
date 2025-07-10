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

use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\CompanyDomainManagementInterface;
use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Ca\Model\Data\Command\Company\Domain
 */
class Delete implements CommandInterface
{
    /**
     * @var CompanyDomainRepositoryInterface
     */
    private $companyDomainRepository;

    /**
     * @var CompanyDomainManagementInterface
     */
    private $companyDomainManagement;

    /**
     * @param CompanyDomainRepositoryInterface $companyDomainRepository
     * @param CompanyDomainManagementInterface $companyDomainManagement
     */
    public function __construct(
        CompanyDomainRepositoryInterface $companyDomainRepository,
        CompanyDomainManagementInterface $companyDomainManagement
    ) {
        $this->companyDomainRepository = $companyDomainRepository;
        $this->companyDomainManagement = $companyDomainManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[CompanyDomainInterface::ID])) {
            throw new \InvalidArgumentException(
                'Company Domain ID param is required to delete'
            );
        }

        if (!isset($data[CompanyDomainInterface::REQUESTED_BY])) {
            throw new \InvalidArgumentException('requested_by argument is required');
        }

        $domain = $this->companyDomainRepository->get($data[CompanyDomainInterface::ID]);
        return $this->companyDomainManagement->deleteDomain(
            $domain,
            $data[CompanyDomainInterface::REQUESTED_BY]
        );
    }
}
