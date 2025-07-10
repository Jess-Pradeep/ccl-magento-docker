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

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyAddressOperationInterface;
use Aheadworks\Ca\Model\Company\Address\Shared\Consumer;
use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddress as CompanySharedAddressResourceModel;
use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddressFactory;
use Aheadworks\Ca\Model\CompanySharedAddressInterface;
use Aheadworks\Ca\Model\CompanySharedAddressInterfaceFactory;
use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddress\CollectionFactory;
use Aheadworks\Ca\Model\Company\Admin\Validator;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\AddressRegistryFactory;

class CompanySharedAddressService implements CompanySharedAddressManagementInterface
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AddressRepositoryInterfaceFactory $addressRepositoryFactory
     * @param Provider $companyUserProvider
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanySharedAddressInterfaceFactory $companySharedAddressFactory
     * @param CollectionFactory $companySharedAddressCollectionFactory
     * @param CompanySharedAddressResourceModel $sharedAddressResourceModel
     * @param CompanySharedAddressFactory $sharedAddressResourceModelFactory
     * @param CompanyAddressOperationInterface $companyAddressOperation
     * @param PublisherInterface $publisher
     * @param Validator $companyAdminValidator
     * @param AddressRegistryFactory $addressRegistryFactory
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $companyUserManagement,
        AddressRepositoryInterfaceFactory $addressRepositoryFactory,
        private readonly Provider $companyUserProvider,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanySharedAddressInterfaceFactory $companySharedAddressFactory,
        private readonly CollectionFactory $companySharedAddressCollectionFactory,
        private readonly CompanySharedAddressResourceModel $sharedAddressResourceModel,
        private readonly CompanySharedAddressFactory $sharedAddressResourceModelFactory,
        private readonly CompanyAddressOperationInterface $companyAddressOperation,
        private readonly PublisherInterface $publisher,
        private readonly Validator $companyAdminValidator,
        private readonly AddressRegistryFactory $addressRegistryFactory
    ) {
        $this->addressRepository = $addressRepositoryFactory->create();
    }

    /**
     * Set shared address flag to company
     *
     * @param bool $isEnabled
     * @param int $companyId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setIsAddressListSharedToAllUsers(bool $isEnabled, int $companyId): void
    {
        $this->companyAdminValidator->validate($isEnabled, $companyId);
        $company = $this->companyRepository->get($companyId);
        $company->setIsSharedAddressesFlagEnabled($isEnabled);
        $this->companyRepository->save($company);
        $this->companyAddressOperation->setCompanyId((int)$company->getId());
        if ($isEnabled) {
            $this->companyAddressOperation->setAction(
                CompanyAddressOperationInterface::ACTION_ADD_SHARED_ADDRESSES
            );
        } else {
            $this->companyAddressOperation->setAction(
                CompanyAddressOperationInterface::ACTION_DELETE_SHARED_ADDRESSES
            );
        }
        $this->publisher->publish(Consumer::TOPIC_NAME, $this->companyAddressOperation);
    }

    /**
     * Copy company admin addresses to all company users
     *
     * @param int $companyId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function copyCompanyAdminAddressListToAllUsers(int $companyId): void
    {
        $company = $this->companyRepository->get($companyId);
        $rootUser = $this->companyUserManagement->getRootUserForCompany($companyId);
        $rootAddresses = $this->companyUserProvider->getCompanyUserAddresses((int)$rootUser->getId());
        $rootAddressesIds = $this->getAddressIds($rootAddresses);
        $sharedAddressResourceModel = $this->sharedAddressResourceModelFactory->create();
        $existingIds = $sharedAddressResourceModel->getExistRootAddressIds($companyId);
        $idsToInsert = array_diff($rootAddressesIds, $existingIds);

        if ($idsToInsert) {
            $companyUsersIds = $this->companyUserManagement->getAllUsersIdsForCompany($company->getId());
            foreach ($rootAddresses as $rootAddress) {
                if (in_array($rootAddress->getId(), $idsToInsert)) {
                    foreach ($companyUsersIds as $companyUserId) {
                        if ($companyUserId != $rootUser->getId()) {
                            $this->addAddressToCompanyUser($rootAddress, (int)$companyUserId, (int)$company->getId());
                        }
                    }
                }
            }
        }
    }

    /**
     * Update company admin addresses to all company users
     *
     * @param int $companyId
     * @param int $rootAddressId
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateCompanyAdminAddressToAllUsers(int $companyId, int $rootAddressId): void
    {
        /** @var AddressRegistry $addressRegistry **/
        $addressRegistry = $this->addressRegistryFactory->create();
        $rootAddress = $addressRegistry->retrieve($rootAddressId);
        $rootAddress = $rootAddress->getDataModel();
        $sharedAddressResourceModel = $this->sharedAddressResourceModelFactory->create();
        $companyUserAddressIds = $sharedAddressResourceModel->getExistCompanyUserAddressIds(
            $companyId,
            (int) $rootAddressId
        );

        if ($companyUserAddressIds) {
            foreach ($companyUserAddressIds as $companyUserAddressId) {
                $this->updateAddressToCompanyUser(
                    $rootAddress,
                    (int)$companyUserAddressId
                );
            }
        }
    }

    /**
     * Delete shared addresses
     *
     * @param int $companyId
     * @param bool $forRootAddressIsNull
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteSharedAddresses(int $companyId, bool $forRootAddressIsNull = false): void
    {
        $collection = $this->companySharedAddressCollectionFactory->create();
        $collection->addFieldToFilter(CompanySharedAddressInterface::COMPANY_ID, $companyId);
        if ($forRootAddressIsNull){
            $collection->addFieldToFilter(CompanySharedAddressInterface::ROOT_ADDRESS_ID, ['null' => true]);
        }

        foreach ($collection as $item) {
            if ($item->getCompanyUserAddressId()) {
                $this->addressRepository->deleteById($item->getCompanyUserAddressId());
            }
            $this->sharedAddressResourceModel->delete($item);
        }
    }

    /**
     * Add shared addresses to new company user
     *
     * @param int $companyId
     * @param int $companyUserId
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addSharedAddressesToNewCompanyUser(int $companyId, int $companyUserId): void
    {
        $company = $this->companyRepository->get($companyId);
        $rootUser = $this->companyUserManagement->getRootUserForCompany($companyId);
        $rootAddresses =  $this->companyUserProvider->getCompanyUserAddresses((int)$rootUser->getId());

        foreach ($rootAddresses as $rootAddress) {
            $this->addAddressToCompanyUser($rootAddress, $companyUserId, (int)$company->getId());
        }
    }

    /**
     * Add address to company user
     *
     * @param AddressInterface $address
     * @param int $companyUserId
     * @param int $companyId
     * @return void
     * @throws LocalizedException
     * @throws AlreadyExistsException
     */
    private function addAddressToCompanyUser(AddressInterface $address, int $companyUserId, int $companyId): void
    {
        $cloneAddress = clone($address);
        $cloneAddress->setId(null);
        $cloneAddress->setCustomerId($companyUserId);
        $newAddress = $this->addressRepository->save($cloneAddress);
        /** @var CompanySharedAddressInterface $companySharedAddress */
        $companySharedAddress = $this->companySharedAddressFactory->create();
        $companySharedAddress->setCompanyId($companyId)
            ->setRootAddressId((int)$address->getId())
            ->setCompanyUserId($companyUserId)
            ->setCompanyUserAddressId((int)$newAddress->getId());

        $this->sharedAddressResourceModel->save($companySharedAddress);
    }

    /**
     * Delete shared addresses to company user
     *
     * @param int $companyId
     * @param int $companyUserId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteSharedAddressesToCompanyUser(int $companyId, int $companyUserId): void
    {
        $collection = $this->companySharedAddressCollectionFactory->create();
        $collection->addFieldToFilter(CompanySharedAddressInterface::COMPANY_ID, $companyId);
        $collection->addFieldToFilter(CompanySharedAddressInterface::COMPANY_USER_ID, $companyUserId);
        $collection->addFieldToFilter(CompanySharedAddressInterface::COMPANY_USER_ID, ['notnull' => true]);

        foreach ($collection as $item) {
            if ($item->getCompanyUserAddressId()) {
                $this->addressRepository->deleteById($item->getCompanyUserAddressId());
            }
            $this->sharedAddressResourceModel->delete($item);
        }
    }

    /**
     * Update address to company user
     *
     * @param AddressInterface $address
     * @param int $companyUserAddressId
     * @return void
     * @throws LocalizedException
     */
    private function updateAddressToCompanyUser(AddressInterface $address, int $companyUserAddressId): void
    {
        $cloneAddress = clone($address);
        $companyUserAddress = $this->addressRepository->getById($companyUserAddressId);
        $cloneAddress->setId($companyUserAddressId);
        $cloneAddress->setCustomerId($companyUserAddress->getCustomerId());
        $this->addressRepository->save($cloneAddress);
    }

    /**
     * Get address ids
     *
     * @param array $rootAddresses
     * @return array|null
     */
    private function getAddressIds(array $rootAddresses): ?array
    {
        $addressIds = [];
        foreach ($rootAddresses as $address) {
            $addressIds[] = $address->getId();
        }
        return $addressIds;
    }
}
