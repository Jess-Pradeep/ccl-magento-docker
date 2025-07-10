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
namespace Aheadworks\Ca\Model\Email\Metadata;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierPool as EmailMetadataModifierPool;

/**
 * Class Builder
 *
 * @package Aheadworks\Ca\Model\Email\Metadata
 */
class Builder
{
    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var EmailMetadataModifierPool
     */
    private $emailMetadataModifierPool;

    /**
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param EmailMetadataModifierPool $emailMetadataModifierPool
     */
    public function __construct(
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        EmailMetadataModifierPool $emailMetadataModifierPool
    ) {
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->emailMetadataModifierPool = $emailMetadataModifierPool;
    }

    /**
     * Build email metadata for notification type
     *
     * @param string $notificationType
     * @param array $relatedObjectList
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    public function build($notificationType, $relatedObjectList)
    {
        /** @var EmailMetadataInterface $emailMetadata */
        $emailMetadata = $this->emailMetadataFactory->create();
        $emailMetadataModifier = $this->emailMetadataModifierPool->getModifierByNotificationType(
            $notificationType
        );
        $emailMetadata = $emailMetadataModifier->addMetadata(
            $emailMetadata,
            $relatedObjectList
        );

        return $emailMetadata;
    }
}
