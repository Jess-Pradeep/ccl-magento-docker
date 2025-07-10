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
namespace Aheadworks\Ctq\ViewModel\Customer;

use Aheadworks\Ctq\Api\BuyerActionManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Order\DataProvider as OrderDataProvider;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\Quote\Action\Type;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status as StatusSource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote as SalesQuote;
use Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca\CompanyManagement;
use Aheadworks\Ctq\Model\History\Notifier\RecipientResolver;

/**
 * Class Quote
 *
 * @package Aheadworks\Ctq\ViewModel\Customer
 */
class Quote implements ArgumentInterface
{
    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var BuyerActionManagementInterface
     */
    private $buyerActionManagement;

    /**
     * @var bool
     */
    private $isEditQuote;

    /**
     * @var bool
     */
    private $isAllowSorting;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CompanyManagement
     */
    private $companyManagement;

    /**
     * @var RecipientResolver
     */
    private $recipientResolver;

    /**
     * @param StatusSource $statusSource
     * @param PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $localeDate
     * @param UrlInterface $urlBuilder
     * @param Url $url
     * @param BuyerActionManagementInterface $buyerActionManagement
     * @param OrderDataProvider $orderDataProvider
     * @param CustomerSession $customerSession
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CompanyManagement $companyManagement
     * @param RecipientResolver $recipientResolver
     */
    public function __construct(
        StatusSource $statusSource,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $localeDate,
        UrlInterface $urlBuilder,
        Url $url,
        BuyerActionManagementInterface $buyerActionManagement,
        OrderDataProvider $orderDataProvider,
        CustomerSession $customerSession,
        QuoteRepositoryInterface $quoteRepository,
        CompanyManagement $companyManagement,
        RecipientResolver $recipientResolver
    ) {
        $this->statusSource = $statusSource;
        $this->priceCurrency = $priceCurrency;
        $this->localeDate = $localeDate;
        $this->urlBuilder = $urlBuilder;
        $this->url = $url;
        $this->buyerActionManagement = $buyerActionManagement;
        $this->orderDataProvider = $orderDataProvider;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->companyManagement = $companyManagement;
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * Retrieve formatted created at date
     *
     * @param string $createdAt
     * @return string
     */
    public function getCreatedAtFormatted($createdAt)
    {
        return $this->localeDate->formatDateTime($createdAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Retrieve formatted order id
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderIdFormatted($orderId)
    {
        return '#' . $this->orderDataProvider->getOrderIncrementId($orderId);
    }

    /**
     * Retrieve order url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Retrieve formatted last updated at date
     *
     * @param string $lastUpdatedAt
     * @return string
     */
    public function getLastUpdatedAtFormatted($lastUpdatedAt)
    {
        return $this->localeDate
            ->formatDateTime($lastUpdatedAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Retrieve formatted expired date
     *
     * @param string $expiredDate
     * @return string
     */
    public function getExpiredDateFormatted($expiredDate)
    {
        return $this->localeDate->formatDateTime($expiredDate, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
    }

    /**
     * Retrieve quote url by path
     *
     * @param string $path
     * @param int $quoteId
     * @return string
     */
    public function getQuoteUrlByPath($path, $quoteId)
    {
        return $this->urlBuilder->getUrl($path, ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve external quote url by path
     *
     * @param string $path
     * @param string $hash
     * @return string
     */
    public function getExternalQuoteUrlByPath($path, $hash)
    {
        return $this->urlBuilder->getUrl($path, ['hash' => $hash]);
    }

    /**
     * Retrieve formatted quote total amount
     *
     * @param float $quoteTotal
     * @return string
     */
    public function getQuoteTotalFormatted($quoteTotal)
    {
        return $this->priceCurrency->convertAndFormat($quoteTotal, false);
    }

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        $statusOptions = $this->statusSource->getOptions();
        return $statusOptions[$status];
    }

    /**
     * Retrieve available quote actions
     *
     * @param QuoteInterface $quote
     * @return QuoteActionInterface[]
     */
    public function getAvailableQuoteActions($quote)
    {
        return $this->buyerActionManagement->getAvailableQuoteActions($quote->getId());
    }

    /**
     * Retrieve available quote actions
     *
     * @param QuoteInterface $quote
     * @param CartInterface|SalesQuote $cart
     * @return QuoteActionInterface[]
     */
    public function prepareBuyerQuoteActions($quote, $cart)
    {
        $actions = $this->buyerActionManagement->getAvailableQuoteActions($quote->getId());
        foreach ($actions as $action) {
            if ($action->getType() == Type::BUY && !$cart->validateMinimumAmount()) {
                $action->setData('is_disabled', true);
            }
        }
        return $actions;
    }

    /**
     * Retrieve edit quote url
     *
     * @param int $quoteId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEditQuoteUrl($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $storeId = $quote->getStoreId();
        return $this->customerSession->isLoggedIn()
            ? $this->url->getQuoteUrl($quoteId, $storeId)
            : $this->url->getExternalQuoteUrl($quote->getHash(), $storeId);
    }

    /**
     * Get quote url by action
     *
     * @param QuoteInterface $quote
     * @param QuoteActionInterface $action
     * @param string $method
     * @return string
     */
    public function getQuoteUrlByAction($quote, $action, $method = Locator::LOCATE_BY_ID)
    {
        if ($method == Locator::LOCATE_BY_HASH) {
            return $this->getExternalQuoteUrlByPath($action->getExternalUrlPath(), $quote->getHash());
        }

        return $this->getQuoteUrlByPath($action->getUrlPath(), $quote->getId());
    }

    /**
     * Check if edit quote or not
     *
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isEditQuote($quote)
    {
        if ($this->isEditQuote === null) {
            $this->isEditQuote = false;
            foreach ($this->getAvailableQuoteActions($quote) as $action) {
                if ($action->getType() == Type::EDIT) {
                    $this->isEditQuote = true;
                    break;
                }
            }
        }
        return $this->isEditQuote;
    }

    /**
     * Check if allow items sorting or not
     *
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isAllowSorting($quote)
    {
        if ($this->isAllowSorting === null) {
            $this->isAllowSorting = false;
            foreach ($this->getAvailableQuoteActions($quote) as $action) {
                if ($action->getType() == Type::EDIT_ITEMS_ORDER) {
                    $this->isAllowSorting = true;
                    break;
                }
            }
        }
        return $this->isAllowSorting;
    }

    /**
     * Retrieve form selector
     *
     * @return string
     */
    public function getFormSelector()
    {
        return '[data-role=aw-ctq-quote-form]';
    }

    /**
     * Get company name
     *
     * @param int|null $customerId
     * @return string
     */
    public function getCompanyName($customerId)
    {
        $companyName = '';
        if ($customerId) {
            $company = $this->companyManagement->getCompanyByCustomerId($customerId);
            $companyName = $company ? $company->getName() : '';
        }

        return $companyName;
    }

    /**
     * Get customer name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function getCustomerName($quote)
    {
        return $this->recipientResolver->resolveBuyerName($quote);
    }
}
