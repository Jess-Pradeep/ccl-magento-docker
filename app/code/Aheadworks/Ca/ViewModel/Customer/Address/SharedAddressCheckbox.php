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

namespace Aheadworks\Ca\ViewModel\Customer\Address;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SharedAddressCheckbox implements ArgumentInterface
{
    /**
     * @param Provider $companyUserProvider
     * @param Session $customerSession
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        private readonly Provider $companyUserProvider,
        private readonly Session $customerSession,
        private readonly CompanyRepositoryInterface $companyRepository,
    ) {
    }

    /**
     * Can show checkbox
     *
     * @return bool
     */
    public function canShowCheckbox(): bool
    {
        $customerId = $this->customerSession->getCustomerId();
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);

        return $companyUser ? $companyUser->getIsRoot() : false;
    }

    /**
     * Is checked
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isChecked(): bool
    {
        $customerId = $this->customerSession->getCustomerId();
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);

        $company = $this->companyRepository->get($companyUser->getCompanyId());

        return $company->getIsSharedAddressesFlagEnabled();
    }
}
