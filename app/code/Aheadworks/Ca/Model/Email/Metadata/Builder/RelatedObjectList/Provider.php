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
namespace Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList;

use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;

/**
 * Class Provider
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList
 */
class Provider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyUserProvider $companyUserProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CompanyUserManagementInterface $companyUserManagement,
        CompanyRepositoryInterface $companyRepository,
        CompanyUserProvider $companyUserProvider
    ) {
        $this->storeManager = $storeManager;
        $this->companyUserManagement = $companyUserManagement;
        $this->companyRepository = $companyRepository;
        $this->companyUserProvider = $companyUserProvider;
    }

    /**
     * Retrieve prepared list of related objects for metadata generation
     *
     * @param CompanyAdminCandidateInterface $candidate
     * @return array
     * @throws NoSuchEntityException
     */
    public function getByCandidate(CompanyAdminCandidateInterface $candidate): array
    {
        $company = $this->companyRepository->get($candidate->getCompanyId());
        $customer = $this->companyUserManagement->getRootUserForCompany($candidate->getCompanyId());

        return [
            ModifierInterface::COMPANY => $company,
            ModifierInterface::CUSTOMER => $customer,
            ModifierInterface::STORE_ID => (int)$customer->getStoreId()
        ];
    }

    /**
     * Retrieve prepared list of related objects for metadata generation
     *
     * @param CompanyDomainInterface $domain
     * @return array
     * @throws NoSuchEntityException
     */
    public function getByDomain($domain)
    {
        $company = $this->companyRepository->get($domain->getCompanyId());
        $customer = $this->companyUserManagement->getRootUserForCompany($domain->getCompanyId());

        return [
            ModifierInterface::DOMAIN => $domain,
            ModifierInterface::COMPANY => $company,
            ModifierInterface::STORE_ID => $customer->getStoreId()
        ];
    }

    /**
     * Retrieve prepared list of related objects for metadata generation
     *
     * @param OrderInterface $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function getByOrder($order)
    {
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($order->getCustomerId());
        return [
            ModifierInterface::ORDER => $order,
            ModifierInterface::COMPANY => $this->companyRepository->get($companyUser->getCompanyId()),
            ModifierInterface::STORE_ID => $order->getStoreId()
        ];
    }

    /**
     * Retrieve prepared list of related objects for metadata generation
     *
     * @param CustomerInterface $customer
     * @return array
     * @throws NoSuchEntityException
     */
    public function getByCustomer($customer)
    {
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customer->getId());
        $company = $this->companyRepository->get($companyUser->getCompanyId());
        return [
            ModifierInterface::CUSTOMER => $customer,
            ModifierInterface::COMPANY => $company,
            ModifierInterface::STORE_ID => $customer->getStoreId()
        ];
    }
}
