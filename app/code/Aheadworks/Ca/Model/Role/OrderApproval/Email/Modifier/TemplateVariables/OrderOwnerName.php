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
namespace Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\TemplateVariables;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\EmailVariables;
use Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientResolver;

/**
 * Class OrderOwnerName
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\TemplateVariables
 */
class OrderOwnerName implements ModifierInterface
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
     * @inheritDoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        /** @var OrderInterface $order */
        $order = $relatedObjectList[ModifierInterface::ORDER];
        $templateVariables[EmailVariables::USER_NAME] = $this->recipientResolver->resolveOrderOwnerName($order);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
