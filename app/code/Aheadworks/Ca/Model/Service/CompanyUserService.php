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
namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyAddressOperationInterface;
use Aheadworks\Ca\Model\Company\Address\Shared\Consumer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\GroupRepositoryInterface;
use Aheadworks\Ca\Model\Customer\Checker\ConvertToCompanyAdmin\Checker as ConvertToCompanyAdminChecker;
use Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;
use Aheadworks\Ca\Model\Customer\Checker\EmailAvailability\Checker as EmailAvailabilityChecker;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\Customer\CompanyUser\CompanyAdmin\AssignmentProcessor;
use Magento\Framework\MessageQueue\PublisherInterface;

class CompanyUserService implements CompanyUserManagementInterface
{
    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param UserContextInterface $userContext
     * @param EmailAvailabilityChecker $emailAvailabilityChecker
     * @param ConvertToCompanyAdminChecker $convertToCompanyAdminChecker
     * @param Notifier $notifier
     * @param ExtensionAttributesBuilder $companyUserExtAttributesBuilder
     * @param HttpContext $httpContext
     * @param Repository $companyUserRepository
     * @param AssignmentProcessor $assignmentProcessor
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyAddressOperationInterface $companyAddressOperation
     * @param PublisherInterface $publisher
     * @param CompanySharedAddressManagementInterface $companySharedAddressService
     */
    public function __construct(
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly AccountManagementInterface $accountManagement,
        private readonly UserContextInterface $userContext,
        private readonly EmailAvailabilityChecker $emailAvailabilityChecker,
        private readonly ConvertToCompanyAdminChecker $convertToCompanyAdminChecker,
        private readonly Notifier $notifier,
        private readonly ExtensionAttributesBuilder $companyUserExtAttributesBuilder,
        private readonly HttpContext $httpContext,
        private readonly Repository $companyUserRepository,
        private readonly AssignmentProcessor $assignmentProcessor,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanyAddressOperationInterface $companyAddressOperation,
        private readonly PublisherInterface $publisher,
        private readonly CompanySharedAddressManagementInterface $companySharedAddressService
    ) {
    }

    /**
     * Retrieve current user
     *
     * @return CustomerInterface|null
     */
    public function getCurrentUser()
    {
        $user = null;
        $userId = $this->userContext->getUserId();

        if (!$userId) {
            $companyInfo = $this->httpContext->getValue('company_info');
            $userId = isset($companyInfo) ? $companyInfo[CompanyUserInterface::CUSTOMER_ID] : null;

            if (!$userId) {
                return $user;
            }
        }

        try {
            $customer = $this->customerRepository->getById($userId);
            if ($customer->getExtensionAttributes()
                && $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ) {
                $user = $customer;
            }
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootUserForCustomer($customerId)
    {
        $rootCustomer = null;
        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $rootCustomer = $this->getRootUserForCompany($companyUser->getCompanyId());
            }
        } catch (\Exception $e) {
            $rootCustomer = null;
        }
        return $rootCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootUserForCompany($companyId)
    {
        $rootUser = null;
        $this->searchCriteriaBuilder
            ->addFilter('aw_ca_customer_by_company_id', $companyId)
            ->addFilter('aw_ca_customer_is_root', null);

        $result = $this->customerRepository->getList($this->searchCriteriaBuilder->create());

        $items = $result->getItems();
        if ($result->getItems()) {
            $rootUser = reset($items);
        }

        return $rootUser;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUserForCompany($companyId)
    {
        $this->searchCriteriaBuilder
            ->addFilter('aw_ca_customer_by_company_id', $companyId);

        $result = $this->customerRepository->getList($this->searchCriteriaBuilder->create());

        return $result->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUsersIdsForCompany($companyId)
    {
        $customerIds = [];
        foreach ($this->getAllUserForCompany($companyId) as $customer) {
            $customerIds[] = $customer->getId();
        }
        return $customerIds;
    }

    /**
     * {@inheritdoc}
     */
    public function saveUser($user)
    {
        if ($user->getId()) {
            return $this->customerRepository->save($user);
        } else {
            $user = $this->accountManagement->createAccount($user);
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
            if (!$companyUser->getIsRoot()) {
                $this->notifier->notify($user, Notifier::NEW_COMPANY_USER_CREATED);
            }
            return $user;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildUsers($userId)
    {
        try {
            $customers = [];
            $customer = $this->customerRepository->getById($userId);
            /** @var CompanyUserInterface $customerCompany */
            if ($customerCompany = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $group = $this->groupRepository->get($customerCompany->getCompanyGroupId());

                $this->searchCriteriaBuilder
                    ->addFilter('aw_ca_customer_by_group_path', $group->getPath());

                $customers = $this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            }
        } catch (\Exception $e) {
            $customers = [];
        }

        return $customers;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildUsersIds($userId)
    {
        $customerIds = [];
        foreach ($this->getChildUsers($userId) as $customer) {
            $customerIds[] = $customer->getId();
        }

        return $customerIds;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailAvailable($email, $websiteId = null)
    {
        return $this->emailAvailabilityChecker->check($email, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableConvertToCompanyAdmin($email, $websiteId = null)
    {
        return $this->convertToCompanyAdminChecker->check($email, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function assignUserToCompany($userId, $companyId, $approvalRequired = false)
    {
        $customer = $this->customerRepository->getById($userId);
        if ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot()
        ) {
            return false;
        }

        $this->companyUserExtAttributesBuilder->setAwCompanyUserIfNotIsset($customer);
        $rootCompanyUser = $this->getRootUserForCompany($companyId);
        if ($customer->getWebsiteId() != $rootCompanyUser->getWebsiteId()) {
            return false;
        }

        $currentCompany = $rootCompanyUser->getExtensionAttributes()->getAwCaCompanyUser();
        $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        $oldCompanyId = null;
        $needDeleteSharedAddressesFlag = false;
        if ($companyUser->getCompanyId()) {
            $oldCompany = $this->companyRepository->get($companyUser->getCompanyId());
            $oldCompanyId = (int)$oldCompany->getId();
            if ($oldCompany->getIsSharedAddressesFlagEnabled()) {
                $needDeleteSharedAddressesFlag = true;
            }
        }

        $companyUser
            ->setCompanyGroupId($currentCompany->getCompanyGroupId())
            ->setCompanyId($companyId);
        if ($approvalRequired) {
            $companyUser->setStatus(Status::PENDING);
        }

        $this->companyUserExtAttributesBuilder->setAdditionalAttributes($customer);
        $this->saveUser($customer);
        if ($approvalRequired) {
            $this->notifier->notify($customer, Notifier::NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER);
            $this->notifier->notify($customer, Notifier::NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN);
        } else {
            $this->notifier->notify($customer, Notifier::NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER);
            $this->notifier->notify($customer, Notifier::NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN);
        }
        if ($needDeleteSharedAddressesFlag) {
            $this->companySharedAddressService->deleteSharedAddressesToCompanyUser(
                $oldCompanyId,
                $userId
            );
        }
        $company = $this->companyRepository->get($companyUser->getCompanyId());
        if ($company->getIsSharedAddressesFlagEnabled()) {
            $this->companySharedAddressService->addSharedAddressesToNewCompanyUser(
                (int)$companyUser->getCompanyId(),
                $userId
            );
        }

        return true;
    }

    /**
     * Unassign user from company
     *
     * @param int $userId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function unassignUserFromCompany(int $userId): bool
    {
        $customer = $this->customerRepository->getById($userId);
        if ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot()
        ) {
            throw new LocalizedException (__('Impossible to unassign a company\'s admin'));
        }

        $rootCompanyUser = $this->getRootUserForCustomer($userId);
        if (!$rootCompanyUser || $customer->getWebsiteId() != $rootCompanyUser->getWebsiteId()) {
            return false;
        }
        $companyUser = $this->companyUserRepository->get($userId);
        $company = $this->companyRepository->get($companyUser->getCompanyId());
        if ($company->getIsSharedAddressesFlagEnabled()) {
            $this->companySharedAddressService->deleteSharedAddressesToCompanyUser(
                (int)$companyUser->getCompanyId(),
                $userId
            );
        }
        $this->companyUserRepository->delete($companyUser);

        $this->notifier->notify($customer, Notifier::COMPANY_USER_UNASSIGNED_FOR_COMPANY_USER);
        $this->notifier->notify($customer, Notifier::COMPANY_USER_UNASSIGNED_FOR_COMPANY_ADMIN);

        return true;
    }

    /**
     * Assign new company admin to company
     *
     * @param int $userId
     * @param int $companyId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function assignNewAdminToCompany(int $userId, int $companyId): bool
    {
        $newCompanyAdmin = $this->customerRepository->getById($userId);
        if ($newCompanyAdmin->getExtensionAttributes()
            && $newCompanyAdmin->getExtensionAttributes()->getAwCaCompanyUser()
        ) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $newCompanyAdmin->getExtensionAttributes()->getAwCaCompanyUser();
            if ($companyUser->getCompanyId() !== $companyId) {
                throw new LocalizedException(__('User from other company cannot be assigned as company admin'));
            }

            if ($companyUser->getIsRoot()) {
                throw new LocalizedException(__('Provided user is already company admin'));
            }
        }
        $company = $this->companyRepository->get($companyId);
        if ($company->getIsSharedAddressesFlagEnabled()) {
            $this->companyAddressOperation->setCompanyId($companyId);
            $this->companyAddressOperation->setAction(
                CompanyAddressOperationInterface::ACTION_DELETE_SHARED_ADDRESSES
            );
            $company->setIsSharedAddressesFlagEnabled(false);
            $this->companyRepository->save($company);
        }

        $this->companyUserExtAttributesBuilder->setAwCompanyUserIfNotIsset($newCompanyAdmin);
        $oldCompanyAdmin = $this->getRootUserForCompany($companyId);
        $this->assignmentProcessor->replaceCompanyAdmin($oldCompanyAdmin, $newCompanyAdmin);

        $this->notifier->notify($newCompanyAdmin, Notifier::NEW_COMPANY_ADMIN_ASSIGNED_FOR_COMPANY_ADMIN);
        if ($this->companyAddressOperation->getAction()) {
            $this->publisher->publish(Consumer::TOPIC_NAME, $this->companyAddressOperation);
        }

        return true;
    }
}
