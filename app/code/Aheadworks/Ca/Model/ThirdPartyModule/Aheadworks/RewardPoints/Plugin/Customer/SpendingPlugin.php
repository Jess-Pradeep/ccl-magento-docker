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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Magento\Quote\Api\Data\AddressInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class SpendingPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class SpendingPlugin
{
    /**
     * @var RewardPointsManagement
     */
    private $rewardPointsManagement;

    /**
     * @param RewardPointsManagement $rewardPointsManagement
     */
    public function __construct(
        RewardPointsManagement $rewardPointsManagement
    ) {
        $this->rewardPointsManagement = $rewardPointsManagement;
    }

    /**
     * Quote item reward points calculation process
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending\Data $spendingData
     * @param AbstractItem $item
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeProcess(
        $subject,
        $spendingData,
        AbstractItem $item,
        $customerId,
        $websiteId
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$spendingData, $item, $customerId, $websiteId];
    }

    /**
     * Distribute reward points at parent item to children items
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param AbstractItem $item
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeDistributeRewardPoints(
        $subject,
        AbstractItem $item,
        $customerId,
        $websiteId
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$item, $customerId, $websiteId];
    }

    /**
     * Shipping reward points calculation process
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending\Data $spendingData
     * @param AddressInterface $address
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeProcessShipping(
        $subject,
        $spendingData,
        AddressInterface
        $address,
        $customerId,
        $websiteId
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$spendingData, $address, $customerId, $websiteId];
    }
}
