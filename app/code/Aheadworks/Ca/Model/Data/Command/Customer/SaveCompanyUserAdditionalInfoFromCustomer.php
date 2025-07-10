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

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class SaveCompanyUserAdditionalInfoFromCustomer
 */
class SaveCompanyUserAdditionalInfoFromCustomer implements CommandInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyUserProvider $companyUserProvider
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CompanyUserProvider $companyUserProvider
    ) {
        $this->customerRepository = $customerRepository;
        $this->companyUserProvider = $companyUserProvider;
    }

    /**
     * @inheritdoc
     *
     */
    public function execute($data)
    {
        if (!isset($data['customer_id'])) {
            return false;
        }
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($data['customer_id']);
        if ($companyUser) {
            $customer = $this->customerRepository->getById($data['customer_id']);
            $companyUser->setAdditionalInfo($data['customer']['aw_ca_customer_additional_info']);
            $customer->getExtensionAttributes()->setAwCaCompanyUser($companyUser);
            $this->customerRepository->save($customer);

            return true;
        }
        return false;
    }
}
