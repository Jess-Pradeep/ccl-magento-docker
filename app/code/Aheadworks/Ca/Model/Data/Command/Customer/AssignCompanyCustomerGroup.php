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
namespace Aheadworks\Ca\Model\Data\Command\Customer;

use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Assign company customer group
 */
class AssignCompanyCustomerGroup implements CommandInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($customer)
    {
        if (!$customer instanceof CustomerInterface) {
            throw new \InvalidArgumentException(
                __('Provided argument must implement %1', CustomerInterface::class)
            );
        }

        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
            $company = $this->companyRepository->get($companyUser->getCompanyId());
            $customer->setGroupId($company->getCustomerGroupId());
            $this->customerRepository->save($customer);

            return true;
        }

        return false;
    }
}
