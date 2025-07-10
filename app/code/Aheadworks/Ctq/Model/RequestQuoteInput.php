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
namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class RequestQuoteInput
 *
 * @package Aheadworks\Ctq\Model
 */
class RequestQuoteInput extends AbstractExtensibleModel implements RequestQuoteInputInterface
{
    /**
     * @inheritdoc
     */
    public function getIsGuestQuote()
    {
        return $this->_getData(self::IS_GUEST_QUOTE);
    }

    /**
     * @inheritdoc
     */
    public function setIsGuestQuote($isGuestQuote)
    {
        return $this->setData(self::IS_GUEST_QUOTE, $isGuestQuote);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteName()
    {
        return $this->_getData(self::QUOTE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteName($quoteName)
    {
        return $this->setData(self::QUOTE_NAME, $quoteName);
    }

    /**
     * @inheritdoc
     */
    public function getComment()
    {
        return $this->_getData(self::COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail()
    {
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFirstName()
    {
        return $this->_getData(self::CUSTOMER_FIRST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerFirstName($firstName)
    {
        return $this->setData(self::CUSTOMER_FIRST_NAME, $firstName);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerLastName()
    {
        return $this->_getData(self::CUSTOMER_LAST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerLastName($lastName)
    {
        return $this->setData(self::CUSTOMER_LAST_NAME, $lastName);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\RequestQuoteInputExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
