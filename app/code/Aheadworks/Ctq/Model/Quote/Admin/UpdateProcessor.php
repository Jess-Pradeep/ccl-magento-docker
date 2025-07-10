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

namespace Aheadworks\Ctq\Model\Quote\Admin;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\QuoteList\Item\CommentApplier;
use Magento\Catalog\Helper\Product;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater\ShippingChecker;
use Magento\Backend\Model\Session\Quote as BackendQuoteSession;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Cart\Checker as CartChecker;

class UpdateProcessor
{
    /**
     * @var RequestInterface|null
     */
    private ?RequestInterface $request = null;

    /**
     * Data persistor form data
     */
    public const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_ctq_quote';

    /**
     * @param Product $productHelper
     * @param QuoteSession $quoteSession
     * @param BackendQuoteSession $backendQuoteSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param QuoteUpdater $quoteUpdater
     * @param ObjectManagerInterface $objectManager
     * @param ShippingChecker $shippingChecker
     * @param CommentApplier $commentApplier
     * @param CartChecker $cartChecker
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Product $productHelper,
        private readonly QuoteSession $quoteSession,
        private readonly BackendQuoteSession $backendQuoteSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly QuoteUpdater $quoteUpdater,
        private readonly ObjectManagerInterface $objectManager,
        private readonly ShippingChecker $shippingChecker,
        private readonly CommentApplier $commentApplier,
        private readonly CartChecker $cartChecker,
        private readonly DataPersistorInterface $dataPersistor
    ) {
        $productHelper->setSkipSaleableCheck(true);
    }

    /**
     * Retrieve gift message save model
     *
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->objectManager->get(\Magento\GiftMessage\Model\Save::class);
    }

    /**
     * Process request
     *
     * @param RequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function processRequest(RequestInterface $request): void
    {
        $this->setRequest($request);
        $this->updateSession();
    }

    /**
     * Update session data
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function updateSession(): void
    {
        $quoteId = $this->getRequest()->getParam('quote')['quote_id'] ?? null;
        if ($quoteId) {
            $this->quoteSession
                ->setConfigKey($quoteId);
        }

        $storeId = (int) $this->getRequest()->getParam('store_id');
        if ($storeId) {
            $this->quoteSession->setStoreId($storeId);
        }
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if ($customerId) {
            if (!$this->quoteSession->getCustomerId()) {
                $this->quoteSession->setCustomerId($customerId);
            }
            if (!$this->quoteSession->getStoreId()) {
                $customer = $this->customerRepository->getById($customerId);
                $this->quoteSession->setStoreId($customer->getStoreId());
            }
        }

        if ($this->quoteSession->getCustomerId()) {
            try {
                $this->customerRepository->getById($this->quoteSession->getCustomerId());
            } catch (NoSuchEntityException $exception) {
                throw new LocalizedException(__('Impossible to update the quote. The customer was deleted.'));
            }
        }

        $currencyId = $this->getRequest()->getParam('currency_id');
        if ($currencyId && $currencyId !== 'false') {
            $this->quoteSession->setCurrencyId((string)$currencyId);
            $this->quoteUpdater->setRecollect(true);
        }
        $ctqQuote = $this->getRequest()->getParam('quote');
        $this->dataPersistor->set(self::DATA_PERSISTOR_FORM_DATA_KEY, $ctqQuote ?? []);
    }

    /**
     * Get quote
     *
     * @return Quote
     */
    public function getQuote(): Quote
    {
        return $this->quoteUpdater->getQuote();
    }

    /**
     * Update native quote session
     *
     * Using to prepare Magento quote once we convert ctq quote into order quote
     *
     * @param QuoteInterface $quote
     * @return void
     */
    public function updateNativeQuoteSession(QuoteInterface $quote): void
    {
        $this->backendQuoteSession->setCustomerId($quote->getCustomerId())
            ->setQuoteId($quote->getCartId())
            ->setStoreId($quote->getStoreId())
            ->setCurrencyId($quote->getCart()->getQuote()['quote_currency_code']);
    }

    /**
     * Update quote data
     *
     * @param string|null $action
     * @return void
     * @throws LocalizedException
     */
    public function updateData(?string $action = null): void
    {
        $this->quoteUpdater->getQuote()->setAwCtqIsNotRequireValidation(true);

        $this->quoteUpdater->setAwCtqQuoteToCart($this->getRequest()->getParams());

        if ($data = $this->getRequest()->getPost('shipping')) {
            if ($action == 'save') {
                $this->shippingChecker->processData($data, $this->quoteUpdater->getQuote());
                $this->quoteUpdater->importShippingInformation($data);
            } else {
                $this->quoteUpdater->importShippingInformation($data);
            }
        }

        /**
         * Initialize catalog rule data
         */
        $this->quoteUpdater->initRuleData();

        /**
         * init first billing address, need for virtual products
         */
        $this->quoteUpdater->getBillingAddress();

        if (!$this->quoteUpdater->getQuote()->isVirtual()) {
            $this->quoteUpdater->setShippingAsBilling(false);
        }

        /**
         * Change shipping address flag
         */
        if (!$this->quoteUpdater->getQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')
        ) {
            $this->quoteUpdater->resetShippingMethod(true);
        }

        /**
         * Reset items calculate flag
         */
        if ($this->getRequest()->getPost('reset_calculation')) {
            $this->quoteUpdater->resetItems();
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->quoteUpdater->getQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')
        ) {
            $this->quoteUpdater->collectShippingRates();
        }

        /**
         * Apply mass changes from sidebar
         */
        if ($data = $this->getRequest()->getPost('sidebar')) {
            $this->quoteUpdater->applySidebarData($data);
        }

        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->quoteUpdater->addProduct($productId, $this->getRequest()->getPostValue());
        }

        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->quoteUpdater->addProducts($items);
        }

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $this->quoteUpdater->updateQuoteItems($items);
        }

        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->quoteUpdater->removeItem($removeItemId, $removeFrom);
            $this->quoteUpdater->recollectCart();
        }

        /**
         * Move quote item
         */
        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        $moveQty = (int)$this->getRequest()->getPost('qty');
        if ($moveItemId && $moveTo) {
            $this->quoteUpdater->moveQuoteItem($moveItemId, $moveTo, $moveQty);
        }

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->quoteUpdater->getQuote()->getPayment()->addData($paymentData);
        }

        $items = $this->quoteUpdater->getQuote()->getAllItems();
        if ($action == 'save' && count($items) === 0) {
            throw new LocalizedException(__('Please specify products.'));
        }
        foreach ($items as $item) {
            $this->commentApplier->applyByItem($item);
        }

        $quoteData = $this->getRequest()->getPost('quote');
        if (isset($quoteData['account'])) {
            $this->quoteUpdater->setAccountData($quoteData['account']);
        }
        if ($action == 'save'
            && $this->cartChecker->checkIfCustomerAccountMustBeCreated($this->quoteUpdater->getQuote())
        ) {
            $this->quoteUpdater->prepareCustomer();
        }

        $this->quoteUpdater->saveQuote();

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->quoteUpdater->getQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Saving of giftmessages
         */
        $giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }

        /**
         * Importing gift message allow items from specific product grid
         */
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonDecode($data)
            );
        }

        /**
         * Importing gift message allow items on update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }

        $data = $this->getRequest()->getPost('order');
        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }
    }

    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    protected function _processFiles(array $items): array
    {
        /* @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->objectManager->get(\Magento\Catalog\Helper\Product::class);
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }

    /**
     * Set request
     *
     * @param RequestInterface $request
     * @return void
     */
    private function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Get request
     *
     * @return RequestInterface|null
     */
    private function getRequest(): ?RequestInterface
    {
        return $this->request;
    }
}
