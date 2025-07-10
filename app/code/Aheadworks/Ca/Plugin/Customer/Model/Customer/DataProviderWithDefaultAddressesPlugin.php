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
namespace Aheadworks\Ca\Plugin\Customer\Model\Customer;

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses;

/**
 * Class DataProviderWithDefaultAddressesPlugin
 * @package Aheadworks\Ca\Plugin\Customer\Model\Customer
 */
class DataProviderWithDefaultAddressesPlugin
{
    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @param CompanyUserProvider $companyUserProvider
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider
    ) {
        $this->companyUserProvider = $companyUserProvider;
    }

    /**
     * Modify results of getData() add field
     *
     * @param DataProviderWithDefaultAddresses $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(
        DataProviderWithDefaultAddresses $subject,
        array $result
    ): array {
        if ($result){
            foreach ($result as $key => $customer) {
                $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($key);
                if ($companyUser) {
                    $result[$key]['customer']['aw_ca_customer_additional_info'] = $companyUser->getAdditionalInfo();
                }
            }
        }

        return $result;
    }
}
