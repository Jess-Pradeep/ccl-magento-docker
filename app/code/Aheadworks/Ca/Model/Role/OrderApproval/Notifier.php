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
namespace Aheadworks\Ca\Model\Role\OrderApproval;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\Email\Metadata\Sender;
use Aheadworks\Ca\Model\Email\Metadata\Builder as EmailMetadataBuilder;
use Aheadworks\Ca\Model\Email\Metadata\Builder\RelatedObjectList\Provider as RelatedObjectListProvider;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
class Notifier
{
    /**#@+
     * Notification type
     */
    const ORDER_WAS_SENT_FOR_APPROVAL = 'order_was_sent_for_approval';
    const ORDER_STATUS_CHANGED = 'order_status_changed';
    /**#@-*/

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var EmailMetadataBuilder
     */
    private $emailMetadataBuilder;

    /**
     * @var RelatedObjectListProvider
     */
    private $relatedObjectListProvider;

    /**
     * @param Sender $sender
     * @param EmailMetadataBuilder $emailMetadataBuilder
     * @param RelatedObjectListProvider $relatedObjectListProvider
     */
    public function __construct(
        Sender $sender,
        EmailMetadataBuilder $emailMetadataBuilder,
        RelatedObjectListProvider $relatedObjectListProvider
    ) {
        $this->sender = $sender;
        $this->emailMetadataBuilder = $emailMetadataBuilder;
        $this->relatedObjectListProvider = $relatedObjectListProvider;
    }

    /**
     * Notify about order approval processes
     *
     * @param OrderInterface $order
     * @param string $notificationType
     * @throws LocalizedException
     */
    public function notify($order, $notificationType)
    {
        $emailMetadata = $this->emailMetadataBuilder->build(
            $notificationType,
            $this->relatedObjectListProvider->getByOrder($order)
        );

        $this->sender->send($emailMetadata);
    }
}
