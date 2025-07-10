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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummaryResource;
use Aheadworks\CreditLimit\Model\CreditSummary\CreditBalance;

/**
 * Class CreditSummary
 *
 * @package Aheadworks\CreditLimit\Model
 */
class CreditSummary extends AbstractModel implements SummaryInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'aw_cl_credit_summary';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CreditBalance $creditBalance
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly CreditBalance $creditBalance,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CreditSummaryResource::class);
    }

    /**
     * @inheritdoc
     */
    public function setSummaryId($summaryId)
    {
        return $this->setData(self::SUMMARY_ID, $summaryId);
    }

    /**
     * @inheritdoc
     */
    public function getSummaryId()
    {
        return $this->getData(self::SUMMARY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCreditLimit($creditLimit)
    {
        return $this->setData(self::CREDIT_LIMIT, $creditLimit);
    }

    /**
     * @inheritdoc
     */
    public function getCreditLimit()
    {
        return $this->getData(self::CREDIT_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setIsCustomCreditLimit($isCustomCreditLimit)
    {
        return $this->setData(self::IS_CUSTOM_CREDIT_LIMIT, $isCustomCreditLimit);
    }

    /**
     * @inheritdoc
     */
    public function getIsCustomCreditLimit()
    {
        return $this->getData(self::IS_CUSTOM_CREDIT_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setIsAllowedToExceed($isAllowed)
    {
        return $this->setData(self::IS_ALLOWED_TO_EXCEED, $isAllowed);
    }

    /**
     * @inheritdoc
     */
    public function getIsAllowedToExceed()
    {
        return $this->getData(self::IS_ALLOWED_TO_EXCEED);
    }

    /**
     * @inheritdoc
     */
    public function setCreditBalance($creditBalance)
    {
        return $this->setData(self::CREDIT_BALANCE, $creditBalance);
    }

    /**
     * @inheritdoc
     */
    public function getCreditBalance()
    {
        return $this->getData(self::CREDIT_BALANCE);
    }

    /**
     * @inheritdoc
     */
    public function setCreditAvailable($creditAvailable)
    {
        return $this->setData(self::CREDIT_AVAILABLE, $creditAvailable);
    }

    /**
     * @inheritdoc
     */
    public function getCreditAvailable()
    {
        return $this->getData(self::CREDIT_AVAILABLE);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setLastPaymentDate($lastPaymentDate)
    {
        return $this->setData(self::LAST_PAYMENT_DATE, $lastPaymentDate);
    }

    /**
     * @inheritdoc
     */
    public function getLastPaymentDate()
    {
        return $this->getData(self::LAST_PAYMENT_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setCompanyId($companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * @inheritdoc
     */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * Set payment period
     *
     * @param int|null $countDays
     * @return SummaryInterface
     */
    public function setPaymentPeriod(?int $countDays): SummaryInterface
    {
        return $this->setData(self::PAYMENT_PERIOD, $countDays);
    }

    /**
     * Get payment period
     *
     * @return int|null
     */
    public function getPaymentPeriod(): ?int
    {
        return $this->getData(self::PAYMENT_PERIOD);
    }

    /**
     * Set due date
     *
     * @param string|null $date
     * @return SummaryInterface
     */
    public function setDueDate(?string $date): SummaryInterface
    {
        return $this->setData(self::DUE_DATE, $date);
    }

    /**
     * Get due date
     *
     * @return string|null
     */
    public function getDueDate(): ?string
    {
        return $this->getData(self::DUE_DATE);
    }

    /**
     * Set last due date
     *
     * @param string|null $date
     * @return SummaryInterface
     */
    public function setLastDueDate(?string $date): SummaryInterface
    {
        return $this->setData(self::LAST_DUE_DATE, $date);
    }

    /**
     * Get last due date
     *
     * @return string|null
     */
    public function getLastDueDate(): ?string
    {
        return $this->getData(self::LAST_DUE_DATE);
    }

    /**
     * Get negative balance date
     *
     * @return null|string
     */
    public function getNegativeBalanceDate(): ?string
    {
        return $this->getData(self::NEGATIVE_BALANCE_DATE);
    }

    /**
     * Set negative balance date
     *
     * @param null|string $negativeBalanceDate
     * @return $this
     */
    public function setNegativeBalanceDate(?string $negativeBalanceDate): self
    {
        return $this->setData(self::NEGATIVE_BALANCE_DATE, $negativeBalanceDate);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws \Exception
     */
    public function beforeSave(): self
    {
        $this->creditBalance->setNegativeBalanceDate($this);

        return parent::beforeSave();
    }
}
