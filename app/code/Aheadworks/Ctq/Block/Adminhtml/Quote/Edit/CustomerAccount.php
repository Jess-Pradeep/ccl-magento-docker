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

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Metadata\FormFactory as MetadataFormFactory;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm;
use Aheadworks\Ctq\Model\Cart\Checker as CartChecker;

/**
 * Create customer account form
 */
class CustomerAccount extends AbstractForm
{
    /**
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param FormFactory $formFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param MetadataFormFactory $metadataFormFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param GroupManagementInterface $groupManagement
     * @param CartChecker $cartChecker
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        FormFactory $formFactory,
        DataObjectProcessor $dataObjectProcessor,
        protected readonly MetadataFormFactory $metadataFormFactory,
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        protected readonly GroupManagementInterface $groupManagement,
        protected readonly CartChecker $cartChecker,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor,
            $data
        );
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass(): string
    {
        return 'head-account';
    }

    /**
     * Return header text
     *
     * @return Phrase
     */
    public function getHeaderText(): Phrase
    {
        return __('Account Information');
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm(): self
    {
        $customerForm = $this->metadataFormFactory->create('customer', 'adminhtml_checkout');

        // prepare customer attributes to show
        $attributes = [];

        // add system required attributes
        foreach ($customerForm->getSystemAttributes() as $attribute) {
            if ($attribute->isRequired()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($attributes['group_id']);
        }

        // add user defined attributes
        foreach ($customerForm->getUserAttributes() as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        $fieldset = $this->_form->addFieldset('main', []);

        $this->_addAttributesToForm($attributes, $fieldset);

        $this->_form->addFieldNameSuffix('quote[account]');
        $this->_form->setValues($this->extractValuesFromAttributes($attributes));

        return $this;
    }

    /**
     * Add additional data to form element
     *
     * @param AbstractElement $element
     * @return $this
     */
    protected function _addAdditionalFormElementData(AbstractElement $element): self
    {
        if ($element->getId() == 'email') {
            $element->setRequired(true);
            $element->setClass('validate-email admin__control-text');
        }

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getFormValues(): array
    {
        try {
            $customer = $this->customerRepository->getById($this->getCustomerId());
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        } catch (\Exception $e) {
            // nothing happens
        }
        $data = isset($customer)
            ? $this->extensibleDataObjectConverter->toFlatArray($customer, [], CustomerInterface::class)
            : [];
        foreach ($this->getQuote()->getData() as $key => $value) {
            if (str_starts_with($key, 'customer_')) {
                $data[substr($key, 9)] = $value;
            }
        }

        if (array_key_exists('group_id', $data) && empty($data['group_id'])) {
            $data['group_id'] = $this->getSelectedGroupId();
        }

        if ($this->getQuote()->getCustomerEmail()) {
            $data['email'] = $this->getQuote()->getCustomerEmail();
        }

        return $data;
    }

    /**
     * Check if customer must be created
     *
     * @return bool
     */
    public function checkIfCustomerAccountMustBeCreated(): bool
    {
        return $this->cartChecker->checkIfCustomerAccountMustBeCreated($this->getQuote());
    }

    /**
     * Extract the form values from attributes.
     *
     * @param array $attributes
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function extractValuesFromAttributes(array $attributes): array
    {
        $formValues = $this->getFormValues();
        foreach ($attributes as $code => $attribute) {
            $defaultValue = $attribute->getDefaultValue();
            if (isset($defaultValue) && !isset($formValues[$code])) {
                $formValues[$code] = $defaultValue;
            }
        }

        return $formValues;
    }

    /**
     * Retrieve selected group id
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getSelectedGroupId(): string
    {
        $selectedGroupId = $this->groupManagement->getDefaultGroup($this->getQuote()->getStoreId())->getId();
        $orderDetails = $this->getRequest()->getParam('order');
        if (!empty($orderDetails) && !empty($orderDetails['account']['group_id'])) {
            $selectedGroupId = $orderDetails['account']['group_id'];
        }

        return $selectedGroupId;
    }
}
