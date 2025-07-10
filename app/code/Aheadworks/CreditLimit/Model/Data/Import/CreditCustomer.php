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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Data\Import;

use Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerInterface;
use Magento\Framework\DataObject;

/**
 * Class CreditCustomer
 */
class CreditCustomer extends DataObject implements CreditCustomerInterface
{
    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Set customer name
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerName(string $name): CreditCustomerInterface
    {
        return $this->setData(self::CUSTOMER_NAME, $name);
    }

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer email
     *
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email): CreditCustomerInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): CreditCustomerInterface
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get amount to add
     *
     * @return float
     */
    public function getAmountToAdd(): float
    {
        return $this->getData(self::AMOUNT_TO_ADD);
    }

    /**
     * Set amount to add
     *
     * @param float $amount
     * @return $this
     */
    public function setAmountToAdd(float $amount): CreditCustomerInterface
    {
        return $this->setData(self::AMOUNT_TO_ADD, $amount);
    }

    /**
     * Get po number
     *
     * @return string|null
     */
    public function getPoNumber(): ?string
    {
        return $this->getData(self::PO_NUMBER);
    }

    /**
     * Set po number
     *
     * @param string|null $number
     * @return $this
     */
    public function setPoNumber($number): CreditCustomerInterface
    {
        return $this->setData(self::PO_NUMBER, $number);
    }

    /**
     * Get comment to customer
     *
     * @return string|null
     */
    public function getCommentToCustomer(): ?string
    {
        return $this->getData(self::COMMENT_TO_CUSTOMER);
    }

    /**
     * Set comment to customer
     *
     * @param string $comment
     * @return $this
     */
    public function setCommentToCustomer($comment): CreditCustomerInterface
    {
        return $this->setData(self::COMMENT_TO_CUSTOMER, $comment);
    }

    /**
     * Get comment to admin
     *
     * @return string|null
     */
    public function getCommentToAdmin(): ?string
    {
        return $this->getData(self::COMMENT_TO_ADMIN);
    }

    /**
     * Set comment to admin
     *
     * @param string $comment
     * @return $this
     */
    public function setCommentToAdmin($comment): CreditCustomerInterface
    {
        return $this->setData(self::COMMENT_TO_ADMIN, $comment);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
