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
namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RequestQuoteInputInterface
 * @api
 */
interface RequestQuoteInputInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const IS_GUEST_QUOTE = 'is_guest_quote';
    const QUOTE_NAME = 'quote_name';
    const COMMENT = 'comment';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_FIRST_NAME = 'customer_first_name';
    const CUSTOMER_LAST_NAME = 'customer_last_name';
    /**#@-*/

    /**
     * Get quote is guest flag
     *
     * @return string
     */
    public function getIsGuestQuote();

    /**
     * Set quote is guest flag
     *
     * @param bool $isGuestQuote
     * @return $this
     */
    public function setIsGuestQuote($isGuestQuote);

    /**
     * Get quote name
     *
     * @return string
     */
    public function getQuoteName();

    /**
     * Set quote is guest flag
     *
     * @param string $quoteName
     * @return $this
     */
    public function setQuoteName($quoteName);

    /**
     * Get comment
     *
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface|null
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface
     * @return $this
     */
    public function setComment($comment);

    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail($email);

    /**
     * Get customer first name
     *
     * @return string|null
     */
    public function getCustomerFirstName();

    /**
     * Set customer first name
     *
     * @param string $firstName
     * @return $this
     */
    public function setCustomerFirstName($firstName);

    /**
     * Get customer first name
     *
     * @return string|null
     */
    public function getCustomerLastName();

    /**
     * Set customer last name
     *
     * @param string $lastName
     * @return $this
     */
    public function setCustomerLastName($lastName);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\RequestQuoteInputExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\RequestQuoteInputExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\RequestQuoteInputExtensionInterface $extensionAttributes
    );
}
