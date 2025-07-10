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
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UnassignCustomer
 */
class UnassignCustomer implements CommandInterface
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Execute command
     *
     * @param mixed $data
     * @return bool
     * @throws LocalizedException
     */
    public function execute($data)
    {
        if (!isset($data[CompanyUserInterface::CUSTOMER_ID])) {
            throw new \InvalidArgumentException(
                'ID param is required to unassign user'
            );
        }

        return $this->companyUserManagement->unassignUserFromCompany($data[CompanyUserInterface::CUSTOMER_ID]);
    }
}
