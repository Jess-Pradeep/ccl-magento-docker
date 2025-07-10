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

namespace Aheadworks\Ca\Model\Data\Command\User;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;

class UnAssignUser implements CommandInterface
{
    /**
     * UnAssignUser Construct
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param Notifier $notifier
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Notifier $notifier
    ) {
    }

   /**
    * Execute command
    *
    * @param array $data
    * @return \Aheadworks\Ca\Model\Customer\CompanyUser
    * @throws LocalizedException
    */
    public function execute($data)
    {
        if (!isset($data['customer_id'])) {
            throw new \InvalidArgumentException('Argument "customer_id" is required');
        }
        if (!isset($data['status'])) {
            throw new \InvalidArgumentException('Argument "status" is required');
        }
        $status = $data['status'];
        if ($status == Status::INACTIVE) {
            $customer = $this->customerRepository->getById($data['customer_id']);
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
            $companyUser->setCompanyUnitId(null);
            $this->customerRepository->save($customer);
        }
       
        return $companyUser;
    }
}
