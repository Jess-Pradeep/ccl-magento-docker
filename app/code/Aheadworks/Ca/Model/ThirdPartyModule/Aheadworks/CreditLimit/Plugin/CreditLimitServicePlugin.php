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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin;

use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface as Subject;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class CreditLimitServicePlugin
 */
class CreditLimitServicePlugin
{
    /**
     * CreditLimitServicePlugin constructor.
     *
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(private CreditLimitManagement $creditLimitManagement)
    {
    }

    /**
     * Change customer id before spend customer credit balance on order
     *
     * @param Subject $subject
     * @param int $customerId
     * @param OrderInterface $order
     * @return array
     */
    public function beforeSpendCreditBalanceOnOrder($subject, $customerId, $order)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded((int)$customerId);
        return [$customerId, $order];
    }
}
