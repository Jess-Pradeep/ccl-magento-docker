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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Aheadworks\RewardPoints\Model\Service\PointsSummaryService;

class PointsSummaryServicePlugin
{
    /**
     * @param RewardPointsManagement $rewardPointsManagement
     */
    public function __construct(
        private readonly RewardPointsManagement $rewardPointsManagement
    ) {
    }

    /**
     * Check reward points limit usage
     *
     * @param PointsSummaryService $subject
     * @param callable $proceed
     * @param int|null $customerId
     * @return int
     */
    public function aroundGetCustomerRewardPointsBalance(
        PointsSummaryService $subject,
        callable $proceed,
        ?int $customerId
    ): int {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        $balance = $proceed($customerId);

        return $this->rewardPointsManagement->applyRewardPointsLimitIfNeeded($balance);
    }
}
