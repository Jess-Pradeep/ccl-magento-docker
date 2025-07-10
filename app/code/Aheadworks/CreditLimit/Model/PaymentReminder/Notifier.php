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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\PaymentReminder;

use Psr\Log\LoggerInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\PaymentReminder\Notifier\EmailProcessor;
use Aheadworks\CreditLimit\Model\Email\Sender;

class Notifier
{
    /**
     * @param LoggerInterface $logger
     * @param Sender $sender
     * @param EmailProcessor $emailProcessor
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Sender $sender,
        private readonly EmailProcessor $emailProcessor
    ) {
    }

    /**
     * Notify the customer about a negative credit balance
     *
     * @param SummaryInterface $summary
     * @return bool
     * @throws \Exception
     */
    public function notify(SummaryInterface $summary): bool
    {
        $emailMetadata = $this->emailProcessor->process($summary);

        try {
            $this->sender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->error(
                "{$e->getMessage()}\n{$e->getTraceAsString()}"
            );
            return false;
        }
        return true;
    }
}
