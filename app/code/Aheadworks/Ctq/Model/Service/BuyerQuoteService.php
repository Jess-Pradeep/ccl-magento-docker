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

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterfaceFactory;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Exception\UpdateForbiddenException;
use Aheadworks\Ctq\Model\Quote\Copier;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ExpirationReminderStatus;
use Aheadworks\Ctq\Api\Data\CartInterface as CtqCartInterface;

/**
 * Class BuyerService
 */
class BuyerQuoteService implements BuyerQuoteManagementInterface
{
    /**
     * @param QuoteInterfaceFactory $quoteFactory
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteManagement $quoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param ExpirationCalculator $expirationCalculator
     * @param Config $config
     * @param Copier $quoteCopier
     * @param CommentInterfaceFactory $commentDataFactory
     * @param RequestQuoteInputInterfaceFactory $requestInputFactory
     */
    public function __construct(
        private readonly QuoteInterfaceFactory $quoteFactory,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly QuoteManagement $quoteManagement,
        private readonly BuyerPermissionManagementInterface $buyerPermissionManagement,
        private readonly ExpirationCalculator $expirationCalculator,
        private readonly Config $config,
        private readonly Copier $quoteCopier,
        private readonly CommentInterfaceFactory $commentDataFactory,
        private readonly RequestQuoteInputInterfaceFactory $requestInputFactory
    ) {}

    /**
     * {@inheritdoc}
     */
    public function requestQuote($cartId, $quoteName, $comment = null)
    {
        if (!$this->buyerPermissionManagement->canRequestQuote($cartId)) {
            throw new LocalizedException(__('You can\'t request a quote.'));
        }

        $requestInput = $this->prepareCustomerQuoteRequestInput($quoteName, $comment);
        return $this->_requestQuote($cartId, $requestInput);
    }

    /**
     * {@inheritdoc}
     */
    public function requestQuoteByRequest($cartId, $requestInput)
    {
        if (!$this->buyerPermissionManagement->canRequestQuote($cartId)) {
            throw new LocalizedException(__('You can\'t request a quote.'));
        }

        if ($requestInput->getIsGuestQuote() === null) {
            $requestInput->setIsGuestQuote(false);
        }

        return $this->_requestQuote($cartId, $requestInput);
    }

    /**
     * {@inheritdoc}
     */
    public function requestQuoteList($cartId, $quoteName, $comment = null)
    {
        if (!$this->buyerPermissionManagement->canRequestQuoteList($cartId)) {
            throw new LocalizedException(__('You can\'t request a quote.'));
        }

        $requestInput = $this->prepareCustomerQuoteRequestInput($quoteName, $comment);
        return $this->_requestQuote($cartId, $requestInput);
    }

    /**
     * {@inheritdoc}
     */
    public function requestQuoteListByRequest($cartId, $requestInput)
    {
        if (!$this->buyerPermissionManagement->canRequestQuoteList($cartId)) {
            throw new LocalizedException(__('You can\'t request a quote.'));
        }

        if ($requestInput->getIsGuestQuote() === null) {
            $requestInput->setIsGuestQuote(false);
        }

        return $this->_requestQuote($cartId, $requestInput);
    }

    /**
     * {@inheritdoc}
     */
    public function updateQuote($quote)
    {
        /** @var CartInterface|Quote $cart */
        $cart = $this->cartRepository->get($quote->getCartId());

        if (!$this->buyerPermissionManagement->isAllowQuoteUpdate($cart->getStore()->getWebsiteId())) {
            throw new UpdateForbiddenException(__('You can\'t update a quote.'));
        }
        return $this->quoteManagement->updateQuote($cart, $quote);
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuoteItemsOrder($quoteId, $sortOrderMap = [])
    {
        if (!$this->buyerPermissionManagement->isAllowItemsSorting($quoteId)) {
            throw new LocalizedException(__('You can\'t change items order.'));
        }

        if (!empty($sortOrderMap)) {
            $this->quoteManagement->changeQuoteItemsOrder($quoteId, $sortOrderMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function copyQuote($quote)
    {
        $copiedCart = $this->quoteCopier->copyCart($quote->getCart());
        $copiedCart->setAwCtqSellerId(null);
        $copiedCart->setIsActive(false);
        $copiedCart->unsetData(CtqCartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID);
        $this->cartRepository->save($copiedCart);
        $autoAcceptEnabled = $this->config->isAutoAcceptEnabled($copiedCart->getStore()->getWebsiteId());
        $status = $autoAcceptEnabled
            ? Status::PENDING_SELLER_REVIEW
            : Status::PENDING_BUYER_REVIEW;

        $copiedQuote = $this->quoteCopier->copyQuote($quote);
        $copiedQuote
            ->setStatus($status)
            ->setOrderId(null)
            ->setHash(null)
            ->setReminderDate($this->expirationCalculator->calculateReminderDate($quote->getStoreId()))
            ->setExpirationDate($this->expirationCalculator->calculateExpirationDate($quote->getStoreId()))
            ->setReminderStatus(ExpirationReminderStatus::READY_TO_BE_SENT);

        $createdQuote = $this->quoteManagement->createQuote($copiedCart, $copiedQuote);

        return $autoAcceptEnabled ? $this->acceptQuote($copiedCart, $createdQuote) : $createdQuote;
    }

    /**
     * {@inheritdoc}
     */
    public function buy($quoteId, $storeId)
    {
        if (!$this->buyerPermissionManagement->canBuyQuote($quoteId)) {
            throw new LocalizedException(__('You can\'t buy the quote.'));
        }

        $cart = $this->quoteManagement->getCartByQuoteId($quoteId);
        try {
            $activeCart = $this->cartRepository->getForCustomer($cart->getCustomerId(), [$storeId]);
            if ($activeCart->getIsActive() && $cart->getId() != $activeCart->getId()) {
                $activeCart->setIsActive(false);
                $this->cartRepository->save($activeCart);
            }
        } catch (NoSuchEntityException $e) {
        }

        $cart->setAwCtqIsNotRequireValidation(true);
        $cart->setAwCtqSellerId(null);
        $cart
            ->setIsCheckoutCart(true)
            ->setStoreId($storeId)
            ->setIsActive(true)
            ->setItems([]);

        $this->quoteManagement->validateCartItemsBeforeBuy($cart);

        $this->cartRepository->save($cart);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCartByQuote($quote, $storeId)
    {
        return $this->quoteManagement->getCartByQuote($quote, true, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function clearCart($cart)
    {
        $cart->setAwCtqIsNotRequireValidation(true);
        $cart->setIsActive(false);
        $this->cartRepository->save($cart);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function changeStatus($quoteId, $status)
    {
        return $this->quoteManagement->changeStatus($quoteId, $status, true);
    }

    /**
     * Request quote
     *
     * @param int $cartId
     * @param RequestQuoteInputInterface $requestInput
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function _requestQuote($cartId, $requestInput)
    {
        /** @var QuoteInterface $quote */
        $quote = $this->quoteFactory->create();

        /** @var CartInterface|Quote $cart */
        $cart = $this->cartRepository->getActive($cartId);
        $customerId = $cart->getCustomerId() ?: $cart->getData(CtqCartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID);
        $cart->setAwCtqIsNotRequireValidation(true);
        $cart->getExtensionAttributes()->setAwCtqQuote($quote);
        $cart->setIsActive(false);
        $cart->setData(CtqCartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID, null);
        if ($requestInput->getIsGuestQuote()) {
            $cart
                ->setCustomerEmail($requestInput->getCustomerEmail())
                ->setCustomerFirstname($requestInput->getCustomerFirstName())
                ->setCustomerLastname($requestInput->getCustomerLastName());
        }

        $this->cartRepository->save($cart);
        if ($requestInput->getIsGuestQuote()) {
            $quote
                ->setCustomerEmail($requestInput->getCustomerEmail())
                ->setCustomerFirstName($requestInput->getCustomerFirstName())
                ->setCustomerLastName($requestInput->getCustomerLastName());
        } else {
            $quote
                ->setCustomerId($customerId)
                ->setCustomerEmail($cart->getCustomerEmail())
                ->setCustomerFirstName($cart->getCustomerFirstname())
                ->setCustomerLastName($cart->getCustomerLastname());
        }

        $quote
            ->setName($requestInput->getQuoteName())
            ->setStatus(Status::PENDING_SELLER_REVIEW)
            ->setReminderDate($this->expirationCalculator->calculateReminderDate($quote->getStoreId()))
            ->setExpirationDate($this->expirationCalculator->calculateExpirationDate($cart->getStoreId()))
            ->setSellerId($this->config->getDefaultSalesRepresentative($customerId))
            ->setComment($requestInput->getComment());
        $quote = $this->quoteManagement->createQuote($cart, $quote);

        return $this->config->isAutoAcceptEnabled($cart->getStore()->getWebsiteId())
            ? $this->acceptQuote($cart, $quote)
            : $quote;
    }

    /**
     * Accept quote
     *
     * @param CartInterface $cart
     * @param QuoteInterface $quote
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function acceptQuote($cart, $quote)
    {
        $acceptComment = $this->config->getAutoAcceptComment($quote->getStoreId());
        /** @var CommentInterface $comment */
        $comment = $this->commentDataFactory->create();

        $comment->setComment($acceptComment);
        $quote
            ->setIsPriceVisible(true)
            ->setStatus(Status::ACCEPTED)
            ->setComment($comment);

        return $this->quoteManagement->updateQuote(
            $cart,
            $quote,
            true
        );
    }

    /**
     * Prepare customer quote request input
     *
     * @param string $quoteName
     * @param CommentInterface $comment
     * @return RequestQuoteInputInterface
     */
    public function prepareCustomerQuoteRequestInput($quoteName, $comment = null)
    {
        /** @var RequestQuoteInputInterface $requestInput */
        $requestInput = $this->requestInputFactory->create();
        $requestInput
            ->setQuoteName($quoteName)
            ->setComment($comment)
            ->setIsGuestQuote(false);

        return $requestInput;
    }
}
