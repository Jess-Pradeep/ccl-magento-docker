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

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Carrier\Custom;
use Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\DiscountCalculatorInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\Metadata\Form as CustomerForm;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Config as TaxConfig;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\DataObject;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater\CtqQuoteSetter;
use Aheadworks\Ctq\Model\Quote\Discount\CurrencyRateConverter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\AdminOrder\Product\Quote\Initializer;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Quote\Model\Quote\Item\Updater as QuoteItemUpdater;
use Magento\Framework\DataObject\Factory;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Customer as AdminQuoteCustomer;

/**
 * Class Updater
 */
//todo future refactoring
class Updater extends DataObject implements CartInterface
{
    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Re-collect cart flag
     *
     * @var boolean
     */
    protected $_needCollectCart = false;

    /**
     * Collect (import) data and validate it flag
     *
     * @var boolean
     */
    protected $_isValidate = false;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Quote associated with the model
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * @var int
     */
    private $itemsCount;

    /**
     * @param ObjectManagerInterface $_objectManager
     * @param Registry $_coreRegistry
     * @param SessionQuote $_session
     * @param LoggerInterface $_logger
     * @param ManagerInterface $messageManager
     * @param Initializer $quoteInitializer
     * @param FormFactory $_metadataFormFactory
     * @param QuoteItemUpdater $quoteItemUpdater
     * @param Factory $objectFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param CtqQuoteSetter $ctqQuoteSetter
     * @param PriceCurrencyInterface $priceCurrency
     * @param CurrencyRateConverter $rateConverter
     * @param TaxConfig $taxConfig
     * @param Custom $customCarrier
     * @param StoreManagerInterface $storeManager
     * @param Customer $adminQuoteCustomer
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private readonly ObjectManagerInterface $_objectManager,
        private readonly Registry $_coreRegistry,
        private readonly SessionQuote $_session,
        private readonly LoggerInterface $_logger,
        private readonly ManagerInterface $messageManager,
        private readonly Initializer $quoteInitializer,
        private readonly FormFactory $_metadataFormFactory,
        private readonly QuoteItemUpdater $quoteItemUpdater,
        private readonly Factory $objectFactory,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly CtqQuoteSetter $ctqQuoteSetter,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly CurrencyRateConverter $rateConverter,
        private readonly TaxConfig $taxConfig,
        private readonly Custom $customCarrier,
        private readonly StoreManagerInterface $storeManager,
        private readonly AdminQuoteCustomer $adminQuoteCustomer,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    /**
     * Set validate data in import data flag
     *
     * @param boolean $flag
     * @return $this
     */
    public function setIsValidate($flag)
    {
        $this->_isValidate = (bool)$flag;
        return $this;
    }

    /**
     * Return is validate data in import flag
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidate()
    {
        return $this->_isValidate;
    }

    /**
     * Retrieve quote item
     *
     * @param int|Item $item
     * @return Item|false
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Item) {
            return $item;
        } elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }

        return false;
    }

    /**
     * Initialize data for price rules
     *
     * @return $this
     */
    public function initRuleData()
    {
        $this->_coreRegistry->register(
            'rule_data',
            new \Magento\Framework\DataObject(
                [
                    'store_id' => $this->_session->getStore()->getId(),
                    'website_id' => $this->_session->getStore()->getWebsiteId(),
                    'customer_group_id' => $this->getCustomerGroupId()
                ]
            ),
            true
        );

        return $this;
    }

    /**
     * Initialize quote extension attributes
     *
     * @param array $data
     * @return $this
     */
    public function setAwCtqQuoteToCart($data)
    {
        /** todo refactoring to composite updater */
        $this->ctqQuoteSetter->setAwCtqQuoteToCart($this->getQuote(), $data);

        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return $this
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }

    /**
     * Recollect totals for customer cart.
     *
     * Set recollect totals flag for quote.
     *
     * @return $this
     */
    public function recollectCart()
    {
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()->collectTotals();
            $this->quoteRepository->save($this->getCustomerCart());
        }
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Quote saving
     *
     * @return $this
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->_needCollect) {
            $this->getQuote()->setTotalsCollectedFlag(false);
            $this->getQuote()->collectTotals();
        }

        $this->quoteRepository->save($this->getQuote());
        return $this;
    }

    /**
     * Retrieve session model object of quote
     *
     * @return \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->getSession()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Set quote object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve current customer group ID.
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }

        return $groupId;
    }

    /**
     * Move quote item to another items list
     *
     * @param int|Item $item
     * @param string $moveTo
     * @param int $qty
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            $removeItem = false;
            $moveTo = explode('_', (string)$moveTo);
            switch ($moveTo[0]) {
                case 'remove':
                    $removeItem = true;
                    break;
                default:
                    break;
            }
            if ($removeItem) {
                $this->getQuote()->deleteItem($item);
            }
            $this->setRecollect(true);
        }

        return $this;
    }

    /**
     * Remove quote item
     *
     * @param int $item
     * @return $this
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Add product to current order quote
     * $product can be either product id or product model
     * $config can be either buyRequest config, or just qty
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param array|float|int|\Magento\Framework\DataObject $config
     * @return $this
     * @throws LocalizedException
     */
    public function addProduct($product, $config = 1)
    {
        if (!is_array($config) && !$config instanceof \Magento\Framework\DataObject) {
            $config = ['qty' => $config];
        }
        $config = new \Magento\Framework\DataObject($config);

        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product;
            $product = $this->_objectManager->create(
                \Magento\Catalog\Model\Product::class
            )->setStore(
                $this->getSession()->getStore()
            )->setStoreId(
                $this->getSession()->getStoreId()
            )->load(
                $product
            );
            if (!$product->getId()) {
                throw new LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }

        $item = $this->quoteInitializer->init($this->getQuote(), $product, $config);

        if (is_string($item)) {
            throw new LocalizedException(__($item));
        }
        $item->checkData();
        $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_RESET);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param array $products
     * @return $this|\Exception
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (double)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * Parse shipping data retrieved from request
     *
     * @param array $data
     * @return $this
     */
    public function importShippingInformation($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);

            if ($this->isEmptyBillingAddress()) {
                $this->setBillingAddress($data['shipping_address']);
            }
        }

        if (isset($data['shipping_method'])) {
            $value = '';
            if (!empty($data['shipping_custom_price'])) {
                $quote = $this->getQuote();
                $value = $this->rateConverter->convertAmountValueToCurrency(
                    'amount',
                    $data['shipping_custom_price'],
                    $quote->getQuoteCurrencyCode(),
                    $quote->getBaseCurrencyCode()
                );
            }

            $this->setShippingMethod($data['shipping_method'], $value);
        }

        return $this;
    }

    /**
     * Check if billing address is empty
     *
     * @return bool
     */
    private function isEmptyBillingAddress()
    {
        $billing = $this->getBillingAddress();

        return empty($billing->getFirstname()) || empty($billing->getLastname())
            || empty($billing->getCity()) || empty($billing->getRegion());
    }

    /**
     * Update quantity of order quote items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|LocalizedException
     */
    public function updateQuoteItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }

        try {
            $this->resetCtqQuote();
            foreach ($items as $itemId => $info) {
                if (!empty($info['configured'])) {
                    unset($info['proposed_price']);
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $info['qty'] = (double)$item->getQty();
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                    $info['proposed_price'] = $this->rateConverter->convertAmountValueToCurrency(
                        DiscountType::PROPOSED_PRICE,
                        $info['proposed_price'],
                        $this->getQuote()->getQuoteCurrencyCode(),
                        $this->getQuote()->getBaseCurrencyCode()
                    );
                }
                $this->quoteItemUpdater->update($item, $info);

                if ($item && !empty($info['action'])) {
                    $this->moveQuoteItem($item, $info['action'], $item->getQty());
                }

                $this->calculateItemPrice($item, $info);
            }
        } catch (LocalizedException $e) {
            $this->recollectCart();
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        $this->recollectCart();

        return $this;
    }

    /**
     * Reset negotiated discount value
     */
    private function resetCtqQuote()
    {
        if ($this->getQuote()->getExtensionAttributes()
            && $this->getQuote()->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $this->getQuote()->getExtensionAttributes()->getAwCtqQuote()->setNegotiatedDiscountValue(0);
        }
    }

    /**
     * Reset calculate flag
     */
    public function resetItems()
    {
        $items = $this->getQuote()->getAllItems();
        foreach ($items as $item) {
            $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_DEFAULT);
        }
    }

    /**
     * Return valid price
     *
     * @param float|int $price
     * @return float|int
     */
    protected function _parseCustomPrice($price)
    {
        $price = $this->_objectManager->get(\Magento\Framework\Locale\FormatInterface::class)->getNumber($price);
        $price = $price > 0 ? $price : 0;

        return $price;
    }

    /**
     * Retrieve order quote shipping address
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Set and validate Quote address
     *
     * All errors added to _errors
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param array $data
     * @return $this
     */
    protected function _setQuoteAddress(\Magento\Quote\Model\Quote\Address $address, array $data)
    {
        $isAjax = !$this->getIsValidate();

        // Region is a Data Object, so it is represented by an array. validateData() doesn't understand arrays, so we
        // need to merge region data with address data. This is going to be removed when we switch to use address Data
        // Object instead of the address model.
        // Note: if we use getRegion() here it will pull region from db using the region_id
        $data = isset($data['region']) && is_array($data['region']) ? array_merge($data, $data['region']) : $data;

        $addressForm = $this->_metadataFormFactory->create(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            'adminhtml_customer_address',
            $data,
            $isAjax,
            CustomerForm::DONT_IGNORE_INVISIBLE,
            []
        );

        // prepare request
        // save original request structure for files
        if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
            $requestData = ['order' => ['shipping_address' => $data]];
            $requestScope = 'order/shipping_address';
        } else {
            $requestData = ['order' => ['billing_address' => $data]];
            $requestScope = 'order/billing_address';
        }
        $request = $addressForm->prepareRequest($requestData);
        $addressData = $addressForm->extractData($request, $requestScope);
        if ($this->getIsValidate()) {
            $errors = $addressForm->validateData($addressData);
            if ($errors !== true) {
                if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                    $typeName = __('Shipping Address: ');
                } else {
                    $typeName = __('Billing Address: ');
                }
                foreach ($errors as $error) {
                    $this->_errors[] = $typeName . $error;
                }
                $address->setData($addressForm->restoreData($addressData));
            } else {
                $address->setData($addressForm->compactData($addressData));
            }
        } else {
            $address->addData($addressForm->restoreData($addressData));
        }

        return $this;
    }

    /**
     * Set shipping address into quote
     *
     * @param \Magento\Quote\Model\Quote\Address|array $address
     * @return $this
     */
    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $shippingAddress = $this->_objectManager->create(
                \Magento\Quote\Model\Quote\Address::class
            )->setData(
                $address
            )->setAddressType(
                \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING
            );
            if (!$this->getQuote()->isVirtual()) {
                $this->_setQuoteAddress($shippingAddress, $address);
            }
        }
        if ($address instanceof \Magento\Quote\Model\Quote\Address) {
            $shippingAddress = $address;
        }

        $this->setRecollect(true);
        $this->getQuote()->setShippingAddress($shippingAddress);

        return $this;
    }

    /**
     * Set shipping address to be same as billing
     *
     * @param bool $flag If true - don't save in address book and actually copy data across billing and shipping
     *                   addresses
     * @return $this
     */
    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0;
            // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * Set billing address into quote
     *
     * @param array $address
     * @return $this
     */
    public function setBillingAddress($address)
    {
        if (!is_array($address)) {
            return $this;
        }

        $billingAddress = $this->_objectManager->create(Address::class)
            ->setData($address)
            ->setAddressType(Address::TYPE_BILLING);

        $this->_setQuoteAddress($billingAddress, $address);

        /**
         * save_in_address_book is not a valid attribute and is filtered out by _setQuoteAddress,
         * that is why it should be added after _setQuoteAddress call
         */
        $saveInAddressBook = (int)(!empty($address['save_in_address_book']));
        $billingAddress->setData('save_in_address_book', $saveInAddressBook);

        $quote = $this->getQuote();
        if (!$quote->isVirtual() && $this->getShippingAddress()->getSameAsBilling()) {
            $address['save_in_address_book'] = 0;
            $this->setShippingAddress($address);
        }

        // not assigned billing address should be saved as new
        // but if quote already has the billing address it won't be overridden
        if (empty($billingAddress->getCustomerAddressId())) {
            $billingAddress->setCustomerAddressId(null);
            $quote->getBillingAddress()->setCustomerAddressId(null);
        }
        $quote->setBillingAddress($billingAddress);

        return $this;
    }

    /**
     * Set shipping method
     *
     * @param string $method
     * @return $this
     */
    public function setShippingMethod($method, $price)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        $this->customCarrier->setAmount((float)$price);
        $this->customCarrier->setChangeAmount(true);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Empty shipping method and clear shipping rates
     *
     * @return $this
     */
    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();

        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     *
     * @return $this
     */
    public function collectShippingRates()
    {
        $store = $this->getQuote()->getStore();
        $this->storeManager->setCurrentStore($store);
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->collectRates();

        return $this;
    }

    /**
     * Calculate totals
     *
     * @return void
     */
    public function collectRates()
    {
        $this->getQuote()->collectTotals();
    }

    /**
     * Add account data to quote
     *
     * @param array $accountData
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setAccountData(array $accountData): self
    {
        $customer = $this->getQuote()->getCustomer();
        if (empty($accountData['email'])) {
            $accountData['email'] = $customer->getEmail();
        }
        $form = $this->adminQuoteCustomer->createCustomerForm($customer);

        // emulate request
        $request = $form->prepareRequest($accountData);
        $requestScope = $request->getPostValue() ? 'quote/account' : null;
        $data = $form->extractData($request, $requestScope);
        $data = $form->restoreData($data);
        $customer = $this->adminQuoteCustomer->createCustomer($data);
        $customer->setStoreId($this->getQuote()->getStoreId());
        $this->getQuote()->updateCustomerData($customer);
        $data = [];

        $customerData = $this->adminQuoteCustomer->convertToFlatArray($customer);
        foreach ($form->getAttributes() as $attribute) {
            $code = sprintf('customer_%s', $attribute->getAttributeCode());
            $data[$code] = isset($customerData[$attribute->getAttributeCode()])
                ? $customerData[$attribute->getAttributeCode()]
                : null;
        }

        if (isset($data['customer_group_id'])) {
            $data['customer_tax_class_id'] = $this->adminQuoteCustomer->getCustomerTaxClass(
                (int)$data['customer_group_id']
            );
            $this->setRecollect(true);
        }

        $this->getQuote()->addData($data);

        return $this;
    }

    /**
     * Prepare customer data for quote creation
     *
     * Create customer if not created using data from customer form.
     * Create customer billing/shipping address if necessary using data from customer address forms.
     * Set customer data to quote
     *
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareCustomer(): self
    {
        $store = $this->getSession()->getStore();
        $customer = $this->getQuote()->getCustomer();

        if ($customer->getId() && !$this->adminQuoteCustomer->isCustomerInStore($this->getQuote(), $store)) {
            /** Create a new customer record if it is not available in the specified store */
            /** Unset customer ID to ensure that new customer will be created */
            $customer
                ->setId(null)
                ->setStoreId($store->getId())
                ->setWebsiteId($store->getWebsiteId())
                ->setCreatedAt(null);
            $customer = $this->adminQuoteCustomer->validateCustomerData($customer, $this->getData());
        } elseif (!$customer->getId()) {
            /** Create new customer */
            $customerBillingAddressDataObject = $this->getBillingAddress()->exportCustomerAddress();
            $customer->setSuffix($customerBillingAddressDataObject->getSuffix())
                ->setFirstname($customerBillingAddressDataObject->getFirstname())
                ->setLastname($customerBillingAddressDataObject->getLastname())
                ->setMiddlename($customerBillingAddressDataObject->getMiddlename())
                ->setPrefix($customerBillingAddressDataObject->getPrefix())
                ->setStoreId($store->getId())
                ->setWebsiteId($store->getWebsiteId());
            $customer = $this->adminQuoteCustomer->validateCustomerData($customer, $this->getData());
        }
        $this->getQuote()->setCustomer($customer);

        if ($this->getBillingAddress()->getSaveInAddressBook()) {
            $this->adminQuoteCustomer->prepareCustomerAddress(
                $this->getQuote()->getCustomer(),
                $this->getBillingAddress(),
                $this->getQuote()
            );
            $address = $this->getBillingAddress()->setCustomerId($this->getQuote()->getCustomer()->getId());
            $this->setBillingAddress($address);
        }
        if (!$this->getQuote()->isVirtual() && $this->getShippingAddress()->getSaveInAddressBook()) {
            $this->_prepareCustomerAddress($this->getQuote()->getCustomer(), $this->getShippingAddress());
            $address = $this->getShippingAddress()->setCustomerId($this->getQuote()->getCustomer()->getId());
            $this->setShippingAddress($address);
        }
        $this->getBillingAddress()->setCustomerId($customer->getId());
        $this->getQuote()->updateCustomerData($this->getQuote()->getCustomer());

        $customer = $this->getQuote()->getCustomer();
        $origAddresses = $customer->getAddresses(); // save original addresses
        $customer->setAddresses([]);
        $customerData = $this->adminQuoteCustomer->convertToFlatArray($customer);
        $customer->setAddresses($origAddresses); // restore original addresses
        foreach ($this->adminQuoteCustomer->createCustomerForm($customer)->getUserAttributes() as $attribute) {
            if (isset($customerData[$attribute->getAttributeCode()])) {
                $quoteCode = sprintf('customer_%s', $attribute->getAttributeCode());
                $this->getQuote()->setData($quoteCode, $customerData[$attribute->getAttributeCode()]);
            }
        }

        return $this;
    }

    /**
     * Calculate Item Price
     *
     * @param Item $item
     * @param array $itemInfo
     * @throws LocalizedException
     */
    private function calculateItemPrice($item, $itemInfo)
    {
        if (isset($itemInfo['proposed_price'])) {
            $this->updatePricePerProduct($item, $itemInfo['proposed_price']);
        } else {
            $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_RESET);
            $ctqQuote = $this->getQuote()->getExtensionAttributes()->getAwCtqQuote();
            if ($ctqQuote->getNegotiatedDiscountType() == DiscountType::PERCENTAGE_DISCOUNT) {
                $ctqQuote->setNegotiatedDiscountValue(
                    $this->priceCurrency->round($ctqQuote->getNegotiatedDiscountValue() / ++$this->itemsCount)
                );
            }
        }
    }

    /**
     * Update proposed price per product
     *
     * @param Item $item
     * @param float $proposedPrice
     * @throws LocalizedException
     */
    private function updatePricePerProduct($item, $proposedPrice)
    {
        $price = $this->taxConfig->priceIncludesTax($item->getStoreId())
            ? $item->getPriceInclTax()
            : $item->getPrice();
        $basePrice = $this->taxConfig->priceIncludesTax($item->getStoreId())
            ? $item->getBasePriceInclTax()
            : $item->getBasePrice();

        if ($proposedPrice < 0 || $price < $proposedPrice) {
            throw new LocalizedException(
                __('Proposed price cannot be less than 0 or higher than catalog price.')
            );
        }

        if ($this->getQuote()->getExtensionAttributes()
            && !$this->getQuote()->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $this->setAwCtqQuoteToCart(
                [
                    'quote' =>
                        [
                            QuoteInterface::NEGOTIATED_DISCOUNT_TYPE => DiscountType::PERCENTAGE_DISCOUNT
                        ]
                ]
            );
        }

        if (!$item->isDeleted()) {
            $amount = $price * $item->getQty() - ($proposedPrice * $item->getQty());
            $baseAmount = $basePrice * $item->getQty() - ($proposedPrice * $item->getQty());
            $percent = $baseAmount * 100 / ($basePrice * $item->getQty());
            $this->itemsCount++;
        } else {
            $amount = 0;
            $baseAmount = 0;
            $percent = 0;
            $proposedPrice = 0;
        }

        /** @var QuoteInterface $ctqQuote */
        $ctqQuote = $this->getQuote()->getExtensionAttributes()->getAwCtqQuote();
        $negotiatedType = $ctqQuote->getNegotiatedDiscountType() ?? DiscountType::PROPOSED_PRICE;
        $negotiatedValue = $ctqQuote->getNegotiatedDiscountValue();

        switch ($negotiatedType) {
            case DiscountType::PERCENTAGE_DISCOUNT:
                //Workaround for calculate average discount percent in quote
                if ($this->itemsCount == $this->getQuoteItemsCount()) {
                    $newNegotiatedValue = $this->priceCurrency->round(
                        ($negotiatedValue + $percent) / $this->itemsCount
                    );
                } else {
                    $newNegotiatedValue = $negotiatedValue + $percent;
                }
                break;

            case DiscountType::PROPOSED_PRICE:
                $newNegotiatedValue = $this->priceCurrency->round(
                    $negotiatedValue + ($proposedPrice * $item->getQty())
                );
                $ctqQuote->setNegotiatedDiscountType($negotiatedType);
                break;

            case DiscountType::AMOUNT_DISCOUNT:
            default:
                $newNegotiatedValue = $negotiatedValue + $amount;
                break;
        }

        $ctqQuote
            ->setNegotiatedDiscountValue($newNegotiatedValue)
            ->setRecollect(true);

        $item
            ->setAwCtqAmount($amount)
            ->setBaseAwCtqAmount($baseAmount)
            ->setAwCtqPercent($percent)
            ->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_PER_ITEM);

        $this
            ->getQuote()
            ->getExtensionAttributes()
            ->setAwCtqQuote($ctqQuote);
    }

    /**
     * Get quote items count not including deleted ones for right percent discount calculation
     *
     * @return int
     */
    private function getQuoteItemsCount()
    {
        $result = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            if ($item->isDeleted()) {
                continue;
            }
            $result++;
        }

        return $result;
    }
}
