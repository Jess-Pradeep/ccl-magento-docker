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

namespace Aheadworks\CreditLimit\Api\Data\Import;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CreditCompanyInterface
 */
interface CreditCompanyInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const COMPANY_NAME = 'company_name';
    const COMPANY_EMAIL = 'company_email';
    const CURRENCY = 'currency';
    const AMOUNT_TO_ADD = 'amount_to_add';
    const PO_NUMBER = 'po_number';
    const COMMENT_TO_CUSTOMER = 'comment_to_customer';
    const COMMENT_TO_ADMIN = 'comment_to_admin';
    /**#@-*/

    /**
     * Get company name
     *
     * @return string|null
     */
    public function getCompanyName(): ?string;

    /**
     * Set company name
     *
     * @param string $name
     * @return $this
     */
    public function setCompanyName(string $name): CreditCompanyInterface;

    /**
     * Get company email
     *
     * @return string
     */
    public function getCompanyEmail(): string;

    /**
     * Set company email
     *
     * @param string $email
     * @return $this
     */
    public function setCompanyEmail(string $email): CreditCompanyInterface;

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Set currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): CreditCompanyInterface;

    /**
     * Get amount to add
     *
     * @return float
     */
    public function getAmountToAdd(): float;

    /**
     * Set amount to add
     *
     * @param float $amount
     * @return $this
     */
    public function setAmountToAdd(float $amount): CreditCompanyInterface;

    /**
     * Get po number
     *
     * @return string|null
     */
    public function getPoNumber(): ?string;

    /**
     * Set po number
     *
     * @param string|null $number
     * @return $this
     */
    public function setPoNumber(?string $number): CreditCompanyInterface;

    /**
     * Get comment to customer
     *
     * @return string|null
     */
    public function getCommentToCustomer(): ?string;

    /**
     * Set comment to customer
     *
     * @param string|null $comment
     * @return $this
     */
    public function setCommentToCustomer(?string $comment): CreditCompanyInterface;

    /**
     * Get comment to admin
     *
     * @return string|null
     */
    public function getCommentToAdmin(): ?string;

    /**
     * Set comment to admin
     *
     * @param string|null $comment
     * @return $this
     */
    public function setCommentToAdmin(?string $comment): CreditCompanyInterface;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyExtensionInterface $extensionAttributes
    );
}
