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
namespace Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\RecipientData;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientResolver;

/**
 * Class OrderOwner
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\RecipientData
 */
class OrderOwner implements ModifierInterface
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
        /** @var OrderInterface $order */
        $order = $relatedObjectList[ModifierInterface::ORDER];
        $emailMetadata->setRecipientEmail($this->recipientResolver->resolveOrderOwnerEmail($order));
        $emailMetadata->setRecipientName($this->recipientResolver->resolveOrderOwnerName($order));

        return $emailMetadata;
    }
}
