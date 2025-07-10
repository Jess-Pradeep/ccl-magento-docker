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

use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AddSharedAddressesToCompanyCustomer implements CommandInterface
{
    /**
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanySharedAddressManagementInterface $companySharedAddressService
     */
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanySharedAddressManagementInterface $companySharedAddressService
    ) {
    }

    /**
     * Execute command
     *
     * @param CustomerInterface $customer
     * @return bool
     * @throws NoSuchEntityException
     */
    public function execute($customer): bool
    {
        if (!$customer instanceof CustomerInterface) {
            throw new \InvalidArgumentException(
                __('Provided argument must implement %1', CustomerInterface::class)
            );
        }

        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();

            $company = $this->companyRepository->get($companyUser->getCompanyId());
            if ($company->getIsSharedAddressesFlagEnabled()) {
                $this->companySharedAddressService->addSharedAddressesToNewCompanyUser(
                    (int)$company->getId(),
                    (int)$companyUser->getCustomerId()
                );
            }
            return true;
        }
        return false;
    }
}
