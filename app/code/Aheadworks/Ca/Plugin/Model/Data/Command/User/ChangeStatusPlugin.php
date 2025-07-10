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

namespace Aheadworks\Ca\Plugin\Model\Data\Command\User;

use Aheadworks\Ca\Model\Data\Command\User\ChangeStatus;
use Aheadworks\Ca\Model\Customer\CompanyUser;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ChangeStatusPlugin
{
    /**
     * ChangeStatusPlugin Constructor
     *
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Unassign Unit after deactive user
     *
     * @param ChangeStatus $subject
     * @param CompanyUser $result
     * @param array $data
     * @return CompanyUser
     */
    public function afterExecute($subject, $result, $data)
    {
        if ($result->getStatus() == \Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status::INACTIVE) {
            $customer = $this->customerRepository->getById($data['customer_id']);
            $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
            $companyUser->setCompanyUnitId(null);
            $this->customerRepository->save($customer);
        }

        return $result;
    }
}
