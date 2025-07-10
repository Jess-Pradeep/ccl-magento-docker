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
use Magento\Sales\Model\Order;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\EmailVariables;

/**
 * Class OrderStatus
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\TemplateVariables
 */
class OrderStatus implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        /** @var Order $order */
        $order = $relatedObjectList[ModifierInterface::ORDER];
        $templateVariables[EmailVariables::ORDER_STATUS] = $order->getStatusLabel();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
