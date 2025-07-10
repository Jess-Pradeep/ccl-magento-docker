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
 * Interface CreditCustomerInterface
 */
interface CreditCustomerInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CURRENCY = 'currency';
    const AMOUNT_TO_ADD = 'amount_to_add';
    const PO_NUMBER = 'po_number';
    const COMMENT_TO_CUSTOMER = 'comment_to_customer';
    const COMMENT_TO_ADMIN = 'comment_to_admin';
    /**#@-*/

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName(): ?string;

    /**
     * Set customer name
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerName(string $name): CreditCustomerInterface;

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * Set customer email
     *
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email): CreditCustomerInterface;

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
    public function setCurrency(string $currency): CreditCustomerInterface;

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
    public function setAmountToAdd(float $amount): CreditCustomerInterface;

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
    public function setPoNumber(?string $number): CreditCustomerInterface;

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
    public function setCommentToCustomer(?string $comment): CreditCustomerInterface;

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
    public function setCommentToAdmin(?string $comment): CreditCustomerInterface;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerExtensionInterface $extensionAttributes
    );
}
