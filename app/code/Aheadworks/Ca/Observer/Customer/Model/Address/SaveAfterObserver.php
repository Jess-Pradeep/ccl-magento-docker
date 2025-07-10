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

namespace Aheadworks\Ca\Observer\Customer\Model\Address;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyAddressOperationInterface;
use Aheadworks\Ca\Model\Company\Address\Shared\Consumer;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider;
use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddressFactory;
use Magento\Customer\Model\Address;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\PublisherInterface;

class SaveAfterObserver implements ObserverInterface
{
    /**
     * @param Provider $companyUserProvider
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanySharedAddressFactory $sharedAddressResourceModelFactory
     * @param PublisherInterface $publisher
     * @param CompanyAddressOperationInterface $companyAddressOperation
     */
    public function __construct(
        private readonly Provider $companyUserProvider,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanySharedAddressFactory $sharedAddressResourceModelFactory,
        private readonly PublisherInterface $publisher,
        private readonly CompanyAddressOperationInterface $companyAddressOperation,
    ) {
    }

    /**
     * Save company user additional info
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();
        $customerAddressId = (int) $customerAddress->getId();
        $customerId = (int)$customerAddress->getCustomerId();
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);
        if ($companyUser && $companyUser->getIsRoot()) {
            $company = $this->companyRepository->get($companyUser->getCompanyId());
            $sharedAddressResourceModel = $this->sharedAddressResourceModelFactory->create();
            $sharedAddressIds = $sharedAddressResourceModel->getExistRootAddressIds((int)$company->getId());
            if ($company->getIsSharedAddressesFlagEnabled()) {
                $this->companyAddressOperation->setCompanyId((int)$company->getId());
                $this->companyAddressOperation->setAddressId($customerAddressId);
                $this->companyAddressOperation->setAction(
                    !in_array($customerAddressId, $sharedAddressIds)
                        ? CompanyAddressOperationInterface::ACTION_ADD_SHARED_ADDRESSES
                        : CompanyAddressOperationInterface::ACTION_UPDATE_SHARED_ADDRESSES
                );

                $this->publisher->publish(Consumer::TOPIC_NAME, $this->companyAddressOperation);
            }
        }
    }
}
