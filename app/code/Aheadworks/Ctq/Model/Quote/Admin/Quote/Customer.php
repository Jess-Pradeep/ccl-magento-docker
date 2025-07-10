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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\Metadata\Form as CustomerForm;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

/**
 * Customer management
 */
class Customer
{
    /**
     * @param AccountManagementInterface $accountManagement
     * @param DataObjectHelper $dataObjectHelper
     * @param FormFactory $metadataFormFactory
     * @param Mapper $customerMapper
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerInterfaceFactory $customerFactory
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        private readonly AccountManagementInterface $accountManagement,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly FormFactory $metadataFormFactory,
        private readonly Mapper $customerMapper,
        private readonly AddressRepositoryInterface $addressRepository,
        private readonly CustomerInterfaceFactory $customerFactory,
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * Check whether we need to create new customer (for another website) during order creation
     *
     * @param Quote $quote
     * @param Store $store
     * @return bool
     * @throws LocalizedException
     */
    public function isCustomerInStore(Quote $quote, Store $store): bool
    {
        $customer = $quote->getCustomer();

        return $customer->getWebsiteId() == $store->getWebsiteId()
            || $this->accountManagement->isCustomerInStore($customer->getWebsiteId(), $store->getId());
    }

    /**
     * Set and validate Customer data. Return the updated Data Object merged with the account data
     *
     * @param CustomerInterface $customer
     * @param array $data
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function validateCustomerData(CustomerInterface $customer, array $data): CustomerInterface
    {
        $form = $this->createCustomerForm($customer);
        // emulate request
        $request = $form->prepareRequest(['order' => $data]);
        $data = $form->extractData($request, 'order/account');
        $validationResults = $this->accountManagement->validate($customer);
        if (!$validationResults->isValid()) {
            $errors = $validationResults->getMessages();
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    throw new LocalizedException(__($error));
                }
            }
        }
        $data = $form->restoreData($data);
        foreach ($data as $key => $value) {
            if ($value !== null) {
                unset($data[$key]);
            }
        }

        $this->dataObjectHelper->populateWithArray(
            $customer,
            $data,
            CustomerInterface::class
        );

        return $customer;
    }

    /**
     * Return Customer (Checkout) Form instance
     *
     * @param CustomerInterface $customer
     * @return CustomerForm
     */
    public function createCustomerForm(CustomerInterface $customer): CustomerForm
    {
        return $this->metadataFormFactory->create(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'adminhtml_checkout',
            $this->convertToFlatArray($customer),
            false,
            CustomerForm::DONT_IGNORE_INVISIBLE
        );
    }

    /**
     * Convert to flat array
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function convertToFlatArray(CustomerInterface $customer): array
    {
        return $this->customerMapper->toFlatArray($customer);
    }

    /**
     * Create customer address and save it in the quote so that it can be used to persist later.
     *
     * @param CustomerInterface $customer
     * @param Address $quoteCustomerAddress
     * @param Quote $quote
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareCustomerAddress(
        CustomerInterface $customer,
        Address $quoteCustomerAddress,
        Quote $quote
    ): void {
        // Possible that customerId is null for new customers
        $quoteCustomerAddress->setCustomerId($customer->getId());
        $customerAddress = $quoteCustomerAddress->exportCustomerAddress();
        $quoteAddressId = $quoteCustomerAddress->getCustomerAddressId();
        $addressType = $quoteCustomerAddress->getAddressType();
        if ($quoteAddressId) {
            /** Update existing address */
            $existingAddressDataObject = $this->addressRepository->getById($quoteAddressId);
            /** Update customer address data */
            $this->dataObjectHelper->mergeDataObjects(
                get_class($existingAddressDataObject),
                $existingAddressDataObject,
                $customerAddress
            );
            $customerAddress = $existingAddressDataObject;
        } elseif ($addressType == Address::ADDRESS_TYPE_SHIPPING) {
            try {
                $billingAddressDataObject = $this->accountManagement->getDefaultBillingAddress($customer->getId());
                // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
            } catch (\Exception $e) {
                /** Billing address does not exist. */
            }
            $isShippingAsBilling = $quoteCustomerAddress->getSameAsBilling();
            if (isset($billingAddressDataObject) && $isShippingAsBilling) {
                /** Set existing billing address as default shipping */
                $customerAddress = $billingAddressDataObject;
                $customerAddress->setIsDefaultShipping(true);
            }
        }

        switch ($addressType) {
            case Address::ADDRESS_TYPE_BILLING:
                if ($customer->getDefaultBilling() === null) {
                    $customerAddress->setIsDefaultBilling(true);
                }
                break;
            case Address::ADDRESS_TYPE_SHIPPING:
                if ($customer->getDefaultShipping() === null) {
                    $customerAddress->setIsDefaultShipping(true);
                }
                break;
            default:
                throw new \InvalidArgumentException('Customer address type is invalid.');
        }

        $quote->setCustomer($customer);
        $quote->addCustomerAddress($customerAddress);
    }

    /**
     * Create customer
     *
     * @param array $customerData
     * @return CustomerInterface
     */
    public function createCustomer(array $customerData): CustomerInterface
    {
        $customer = $this->customerFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customer,
            $customerData,
            CustomerInterface::class
        );

        return $customer;
    }

    /**
     * Get customer tax class
     *
     * @param int $groupId
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerTaxClass(int $groupId): int
    {
        $customerGroup = $this->groupRepository->getById($groupId);
        return (int)$customerGroup->getTaxClassId();
    }
}
