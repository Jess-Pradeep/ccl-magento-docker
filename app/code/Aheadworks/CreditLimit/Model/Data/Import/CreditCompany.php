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

use Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyInterface;
use Magento\Framework\DataObject;

/**
 * Class CreditCompany
 */
class CreditCompany extends DataObject implements CreditCompanyInterface
{
    /**
     * Get Company name
     *
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->getData(self::COMPANY_NAME);
    }

    /**
     * Set Company name
     *
     * @param string $name
     * @return $this
     */
    public function setCompanyName(string $name): CreditCompanyInterface
    {
        return $this->setData(self::COMPANY_NAME, $name);
    }

    /**
     * Get Company email
     *
     * @return string
     */
    public function getCompanyEmail(): string
    {
        return $this->getData(self::COMPANY_EMAIL);
    }

    /**
     * Set Company email
     *
     * @param string $email
     * @return $this
     */
    public function setCompanyEmail(string $email): CreditCompanyInterface
    {
        return $this->setData(self::COMPANY_EMAIL, $email);
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
    public function setCurrency(string $currency): CreditCompanyInterface
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
    public function setAmountToAdd(float $amount): CreditCompanyInterface
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
    public function setPoNumber($number): CreditCompanyInterface
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
    public function setCommentToCustomer($comment): CreditCompanyInterface
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
    public function setCommentToAdmin($comment): CreditCompanyInterface
    {
        return $this->setData(self::COMMENT_TO_ADMIN, $comment);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
