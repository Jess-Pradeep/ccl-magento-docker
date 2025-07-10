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
namespace Aheadworks\CreditLimit\Model\Customer\Notifier;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterface;

/**
 * Interface EmailProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier
 */
interface EmailProcessorInterface
{
    /**
     * Process email
     *
     * @param int $customerId
     * @param TransactionInterface $transaction
     * @return EmailMetadataInterface|bool
     */
    public function process($customerId, $transaction);
}
