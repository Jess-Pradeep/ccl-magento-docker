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
namespace Aheadworks\Ca\Model\Customer\Checker\EmailAvailability;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterfaceFactory;
use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Company\Domain\Search\Builder as DomainSearchBuilder;
use Aheadworks\Ca\Model\Company\Domain\Resolver as DomainResolver;

/**
 * Class Checker
 *
 * @package Aheadworks\Ca\Model\Customer\Checker\EmailAvailability
 */
class Checker
{
    /**
     * @var EmailAvailabilityResultInterfaceFactory
     */
    private $availabilityResultFactory;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var Share
     */
    private $customerShareConfig;

    /**
     * @var DomainSearchBuilder
     */
    private $domainSearchBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DomainResolver
     */
    private $domainResolver;

    /**
     * @param EmailAvailabilityResultInterfaceFactory $availabilityResultFactory
     * @param AccountManagementInterface $accountManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CompanyRepositoryInterface $companyRepository
     * @param Share $customerShareConfig
     * @param DomainSearchBuilder $domainSearchBuilder
     * @param StoreManagerInterface $storeManager
     * @param DomainResolver $domainResolver
     */
    public function __construct(
        EmailAvailabilityResultInterfaceFactory $availabilityResultFactory,
        AccountManagementInterface $accountManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CompanyRepositoryInterface $companyRepository,
        Share $customerShareConfig,
        DomainSearchBuilder $domainSearchBuilder,
        StoreManagerInterface $storeManager,
        DomainResolver $domainResolver
    ) {
        $this->availabilityResultFactory = $availabilityResultFactory;
        $this->accountManagement = $accountManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->customerShareConfig = $customerShareConfig;
        $this->domainSearchBuilder = $domainSearchBuilder;
        $this->storeManager = $storeManager;
        $this->domainResolver = $domainResolver;
    }

    /**
     * Check if email is available
     *
     * @param string $email
     * @param int|null $website
     * @throws LocalizedException
     * @return EmailAvailabilityResultInterface
     */
    public function check($email, $website = null)
    {
        $isAvailableForCustomer = $this->accountManagement->isEmailAvailable($email, $website);
        $isAvailableForCompany = $this->isEmailAvailableForCompany($email, $website);

        return $this->availabilityResultFactory->create(
            [
                'isAvailableForCustomer' => $isAvailableForCustomer,
                'isAvailableForCompany' => $isAvailableForCompany
            ]
        );
    }

    /**
     * Check if email is available for company
     *
     * @param string $email
     * @param int|null $website
     * @return bool
     * @throws LocalizedException
     */
    public function isEmailAvailableForCompany($email, $website = null)
    {
        if ($this->isDomainExists($email)) {
            return false;
        }

        if ($website === null) {
            $website = $this->storeManager->getStore()->getWebsiteId();
        }

        if ($this->customerShareConfig->isWebsiteScope()) {
            $this->searchCriteriaBuilder->addFilter('website', $website);
        }
        $this->searchCriteriaBuilder->addFilter(CompanyInterface::EMAIL, $email);
        $companyList = $this->companyRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return empty($companyList);
    }

    /**
     * Check if domain exists for company email
     *
     * @param string $companyEmail
     * @return bool
     * @throws LocalizedException
     */
    private function isDomainExists($companyEmail)
    {
        $domainName = $this->domainResolver->resolveFromEmail($companyEmail);
        $this->domainSearchBuilder->addNameFilter($domainName);
        $domainList = $this->domainSearchBuilder->searchDomains();

        return count($domainList) > 0;
    }
}
