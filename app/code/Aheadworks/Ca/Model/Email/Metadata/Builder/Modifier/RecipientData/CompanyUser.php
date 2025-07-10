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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientData;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientResolver;

/**
 * Class CompanyUser
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientData
 */
class CompanyUser implements ModifierInterface
{
    /**
     * @var RecipientResolver
     */
    private $recipientResolver;

    /**
     * @param RecipientResolver $recipientResolver
     */
    public function __construct(
        RecipientResolver $recipientResolver
    ) {
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $relatedObjectList[ModifierInterface::CUSTOMER];
        $emailMetadata->setRecipientEmail($this->recipientResolver->resolveCustomerEmail($customer->getId()));
        $emailMetadata->setRecipientName($this->recipientResolver->resolveCustomerName($customer->getId()));

        return $emailMetadata;
    }
}
