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

namespace Aheadworks\Ca\Model\Data\Command\Company;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Process saving command
 */
class ProcessSaving implements CommandInterface
{
    public const RESULT_UPDATED = 'is_updated';
    public const RESULT_CREATED = 'is_created';

    /**
     * @param SellerCompanyManagementInterface $sellerCompanyService
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly SellerCompanyManagementInterface $sellerCompanyService,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * Execute command
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function execute($data): array
    {
        if (!isset($data['customer'])) {
            throw new \InvalidArgumentException('Customer param is required to save company');
        }

        if (!isset($data['company'])) {
            throw new \InvalidArgumentException('Company param is required to save company');
        }

        $company = $data['company'];
        $customer = $data['customer'];

        $result = [
            self::RESULT_CREATED => false,
            self::RESULT_UPDATED => false
        ];

        if (!$company->getId()) {
            $customer = $this->updateCustomerIfAvailableConvertToCompanyAdmin($customer);
            $this->sellerCompanyService->createCompany($company, $customer);
            $result[self::RESULT_CREATED] = true;
        } else {
            $this->sellerCompanyService->updateCompany($company, $customer);
            $result[self::RESULT_UPDATED] = true;
        }

        return $result;
    }

    /**
     * Update customer if available convert to company admin
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws LocalizedException
     */
    private function updateCustomerIfAvailableConvertToCompanyAdmin(CustomerInterface $customer): CustomerInterface
    {
        if ($this->companyUserManagement->isAvailableConvertToCompanyAdmin(
            $customer->getEmail(),
            $customer->getWebsiteId()
        )) {
            $existingCustomer = $this->customerRepository->get($customer->getEmail(), $customer->getWebsiteId());
            $existingCustomer
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname());
            return $existingCustomer;
        }
        return $customer;
    }
}
