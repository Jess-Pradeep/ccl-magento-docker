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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class CustomerServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin
 */
class CustomerServicePlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Check if credit limit is allowed
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param callable $proceed
     * @param int $customerId
     * @return bool
     */
    public function aroundIsCreditLimitAvailable($subject, callable $proceed, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        $isAllowed = $proceed($customerId);

        return $isAllowed && $this->creditLimitManagement->isAvailableViewAndUse();
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeIsAllowedToUpdateCreditBalance($subject, $customerId, $websiteId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId];
    }

    /**
     * Change customer to company admin if required
     *
     * @param CustomerManagementInterface $subject
     * @param int $customerId
     * @return array
     */
    public function beforeGetCreditLimitSummary($subject, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId];
    }
}
