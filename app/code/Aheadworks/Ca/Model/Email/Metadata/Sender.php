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

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Sender as EmailSender;

/**
 * Class Sender
 *
 * @package Aheadworks\Ca\Model\Email\Metadata
 */
class Sender
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EmailSender
     */
    private $sender;

    /**
     * @param LoggerInterface $logger
     * @param EmailSender $sender
     */
    public function __construct(
        LoggerInterface $logger,
        EmailSender $sender
    ) {
        $this->logger = $logger;
        $this->sender = $sender;
    }

    /**
     * Send email
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return bool
     * @throws LocalizedException
     */
    public function send($emailMetadata)
    {
        try {
            if ($emailMetadata->getRecipientEmail()) {
                $this->sender->send($emailMetadata);
                return true;
            }
        } catch (MailException $e) {
            $this->logger->critical($e);
        }

        return false;
    }
}
