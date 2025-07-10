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

namespace Aheadworks\Ca\Observer\Customer\Model\Address;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider;
use Magento\Customer\Model\Address;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class DeleteAfterObserver implements ObserverInterface
{
    /**
     * @param Provider $companyUserProvider
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanySharedAddressManagementInterface $companySharedAddressService
     */
    public function __construct(
        private readonly Provider $companyUserProvider,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanySharedAddressManagementInterface $companySharedAddressService
    ) {
    }

    /**
     * Save company user additional info
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();
        $customerId = $customerAddress->getCustomerId();
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);
        if ($companyUser && $companyUser->getIsRoot()) {
            $company = $this->companyRepository->get($companyUser->getCompanyId());
            if ($company->getIsSharedAddressesFlagEnabled()) {
                $this->companySharedAddressService->deleteSharedAddresses((int)$company->getId(), true);
            }
        }
    }
}
