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

namespace Aheadworks\Ctq\ViewModel\Quote\Edit\Form;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca\CompanyManagement;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Model\Source\Quote\Status as StatusSource;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Model\Source\Admin\User as AdminUserSource;
use Aheadworks\Ctq\Model\Config;
use Magento\Customer\Api\GroupRepositoryInterface;
use Aheadworks\Ctq\Model\Order\DataProvider as OrderDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Quote\Admin\UpdateProcessor;

class QuoteInformation implements ArgumentInterface
{
    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param UrlInterface $urlBuilder
     * @param TimezoneInterface $localeDate
     * @param QuoteSession $quoteSession
     * @param StatusSource $statusSource
     * @param ExpirationCalculator $expirationCalculator
     * @param QuoteInterfaceFactory $quoteFactory
     * @param AdminUserSource $adminUserSource
     * @param Config $config
     * @param GroupRepositoryInterface $groupRepository
     * @param OrderDataProvider $orderDataProvider
     * @param StoreManagerInterface $storeManager
     * @param CompanyManagement $companyManagement
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $quoteRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly UrlInterface $urlBuilder,
        private readonly TimezoneInterface $localeDate,
        private readonly QuoteSession $quoteSession,
        private readonly StatusSource $statusSource,
        private readonly ExpirationCalculator $expirationCalculator,
        private readonly QuoteInterfaceFactory $quoteFactory,
        private readonly AdminUserSource $adminUserSource,
        private readonly Config $config,
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly OrderDataProvider $orderDataProvider,
        private readonly StoreManagerInterface $storeManager,
        private readonly CompanyManagement $companyManagement,
        private readonly DataPersistorInterface $dataPersistor
    ) {
    }

    /**
     * Check if quote name is required
     *
     * Used as extension point for plugins to disable validation when necessary
     *
     * @return bool
     */
    public function isQuoteNameRequired(): bool
    {
        return true;
    }

    /**
     * Get quote model
     *
     * @param int $quoteId
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getQuote($quoteId)
    {
        return $quoteId ? $this->quoteRepository->get($quoteId) : $this->quoteFactory->create();
    }

    /**
     * Get quote store name
     *
     * @param QuoteInterface $quote
     * @return null|string
     */
    public function getQuoteStoreName($quote)
    {
        if ($quote) {
            $storeId = $quote->getStoreId() ?: $this->quoteSession->getStore()->getStoreId();

            if ($storeId) {
                $store = $this->storeManager->getStore($storeId);
                $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];

                return implode('<br/>', $name);
            }
        }

        return null;
    }

    /**
     * Get customer name
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerName()
    {
        $customerName = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerName;
    }

    /**
     * Get company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        $companyName = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $company = $this->companyManagement->getCompanyByCustomerId($customerId);
            $companyName = $company ? $company->getName() : '';
        }

        return $companyName;
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
     * Is price visibility column enabled
     *
     * @return bool
     */
    public function isPriceVisibilityColumnEnabled()
    {
        return false;
    }

    /**
     * Get customer group
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerGroup()
    {
        $customerGroup = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $groupId = $customer->getGroupId();
                $customerGroup = $this->groupRepository->getById($groupId)->getCode();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerGroup;
    }

    /**
     * Get customer name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerEmail()
    {
        $customerEmail = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $customerEmail = $customer->getEmail();
            } catch (NoSuchEntityException $e) {
            }
        }

        return $customerEmail;
    }

    /**
     * Get link to customer edit form in backend
     *
     * @return string
     */
    public function getLinkToCustomerEditForm()
    {
        $link = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $link = $this->urlBuilder->getUrl('customer/index/edit', ['id' => $customerId]);
        }

        return $link;
    }

    /**
     * Get link to company edit form in backend
     *
     * @return string
     */
    public function getLinkToCompanyEditForm()
    {
        $link = '';
        $customerId = $this->quoteSession->getCustomerId();
        if ($customerId) {
            $company = $this->companyManagement->getCompanyByCustomerId($customerId);
            if ($company) {
                $link = $this->urlBuilder->getUrl('aw_ca/company/edit', ['id' => $company->getId()]);
            }
        }

        return $link;
    }

    /**
     * Retrieve short date format
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->localeDate->getDateFormat();
    }

    /**
     * Prepare expiration date
     *
     * @param QuoteInterface $quote
     * @return string
     * @throws \Exception
     */
    public function prepareExpirationDate($quote)
    {
        $persistedValue = $this->getPersistedValue(QuoteInterface::EXPIRATION_DATE);
        if ($persistedValue) {
            return $persistedValue;
        }

        return $quote->getId()
            ? $quote->getExpirationDate()
            : $this->expirationCalculator->calculateExpirationDate($this->quoteSession->getStoreId());
    }

    /**
     * Prepare reminder date
     *
     * @param QuoteInterface $quote
     * @return string|null
     * @throws \Exception
     */
    public function prepareReminderDate(QuoteInterface $quote): ?string
    {
        $persistedValue = $this->getPersistedValue(QuoteInterface::REMINDER_DATE);
        if ($persistedValue) {
            return $persistedValue;
        }
        return $quote->getId()
            ? $quote->getReminderDate()
            : $this->expirationCalculator->calculateReminderDate(
                $this->quoteSession->getStoreId() ? (int)$this->quoteSession->getStoreId() : null
            );
    }

    /**
     * Prepare quote status
     *
     * @param string $statusCode
     * @return string
     */
    public function prepareQuoteStatus($statusCode)
    {
        $options = $this->statusSource->getOptions();
        return $options[$statusCode] ?? '';
    }

    /**
     * Get list of admin users
     *
     * @return array
     */
    public function getListOfAdminUsers()
    {
        return $this->adminUserSource->toOptionArray();
    }

    /**
     * Is admin user select options is chosen
     *
     * @param array $adminUser
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isAdminUserOptionSelected($adminUser, $quote)
    {
        $quoteSellerId = $this->getPersistedValue('seller_id') ?: $quote->getSellerId();
        if ($quoteSellerId) {
            return $adminUser['value'] == $quoteSellerId;
        }

        return $adminUser['value'] == $this->config->getQuoteAssignedAdminUser();
    }

    /**
     * Get persisted value
     *
     * @param string $fieldName
     * @return string
     */
    public function getPersistedValue(string $fieldName): string
    {
        $dataFromForm = $this->dataPersistor->get(UpdateProcessor::DATA_PERSISTOR_FORM_DATA_KEY);
        if (empty($dataFromForm) || (!is_array($dataFromForm))) {
            return '';
        }

        return $dataFromForm[$fieldName] ?? '';
    }
}
