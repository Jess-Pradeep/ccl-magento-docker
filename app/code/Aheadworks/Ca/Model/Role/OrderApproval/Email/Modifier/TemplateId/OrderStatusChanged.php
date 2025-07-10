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
namespace Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\TemplateId;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Config;

/**
 * Class OrderStatusChanged
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval\Email\Modifier\TemplateId
 */
class OrderStatusChanged implements ModifierInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        /** @var OrderInterface $order */
        $order = $relatedObjectList[ModifierInterface::ORDER];
        $emailMetadata->setTemplateId(
            $this->config->getOrderStatusChangedTemplate($order->getStoreId())
        );

        return $emailMetadata;
    }
}
