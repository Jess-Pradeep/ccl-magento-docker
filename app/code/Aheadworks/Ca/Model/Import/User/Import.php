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

namespace Aheadworks\Ca\Model\Import\User;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterfaceFactory;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\ImportExport\Model\Import\AbstractSource;

class Import
{
    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CompanyUserProvider $companyUserProvider
     * @param CompanyUserInterfaceFactory $companyUserFactory
     * @param RequestInterfaceFactory $requestFactory
     * @param CustomerExtractor $customerExtractor
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly CompanyUserProvider $companyUserProvider,
        private readonly CompanyUserInterfaceFactory $companyUserFactory,
        private readonly RequestInterfaceFactory $requestFactory,
        private readonly CustomerExtractor $customerExtractor,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanyUserManagementInterface $companyUserManagement
    ) {
    }

    /**
     * Process import
     *
     * @param AbstractSource $source
     * @return array
     * @return array
     */
    public function process(AbstractSource $source): array
    {
        $errors = [];
        $source->rewind();
        while ($source->valid()) {
            try {
                $rowData = $source->current();
                $user = $this->prepareUser($rowData);
                $this->companyUserManagement->saveUser($user);
                $source->next();
            } catch (\Exception $exception) {
                $message = __(
                    'An error occurred while importing user with email "%1": %2',
                    $rowData[CustomerInterface::EMAIL] ?? '',
                    $exception->getMessage()
                );
                $errors[] = $message;
                $source->next();
            }
        }

        return $errors;
    }

    /**
     * Prepare user
     *
     * @param array $rowData
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    private function prepareUser(array $rowData): CustomerInterface
    {
        /** @var AbstractModel $companyUser */
        $newCompanyUser = $this->companyUserFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $newCompanyUser,
            $rowData,
            CompanyUserInterface::class
        );

        $newCompanyUserData = $newCompanyUser->getData();
        $currentCompanyUser = $this->companyUserProvider->getCurrentCompanyUser();
        $company = $this->companyRepository->get($currentCompanyUser->getCompanyId());

        $newCompanyUserData[CompanyUserInterface::COMPANY_GROUP_ID] = $company->getCustomerGroupId();
        $newCompanyUserData[CompanyUserInterface::COMPANY_ID] = $company->getId();

        $rowData['extension_attributes']['aw_ca_company_user'] = $newCompanyUserData;
        $request = $this->requestFactory->create();
        $request->setParams($rowData);
        $user = $this->customerExtractor->extract('customer_account_create', $request);

        $this->dataObjectHelper->populateWithArray(
            $user,
            $rowData,
            CustomerInterface::class
        );
        $user->setGroupId($company->getCustomerGroupId());

        return $user;
    }
}
