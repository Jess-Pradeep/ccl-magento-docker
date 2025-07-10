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

namespace Aheadworks\Ca\Plugin\Model\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;

class AccountManagementPlugin
{
    /**
     * @param CommandInterface $checkDomainAndAssignToCompanyCommand
     * @param CommandInterface $assignCompanyCustomerGroup
     * @param CommandInterface $addSharedAddressesToCompanyCustomer
     */
    public function __construct(
        private readonly CommandInterface $checkDomainAndAssignToCompanyCommand,
        private readonly CommandInterface $assignCompanyCustomerGroup,
        private readonly CommandInterface $addSharedAddressesToCompanyCustomer
    ) {
    }

    /**
     * Check created customer and move it to company if required
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $resultCustomer
     * @return CustomerInterface
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateAccountWithPasswordHash(AccountManagementInterface $subject, $resultCustomer)
    {
        $this->checkDomainAndAssignToCompanyCommand->execute($resultCustomer);
        $this->assignCompanyCustomerGroup->execute($resultCustomer);
        $this->addSharedAddressesToCompanyCustomer->execute($resultCustomer);

        return $resultCustomer;
    }
}
