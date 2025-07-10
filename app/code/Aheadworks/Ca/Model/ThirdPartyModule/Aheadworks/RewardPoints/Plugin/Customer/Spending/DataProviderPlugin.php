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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer\Spending;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;

/**
 * Class DataProviderPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer\Spending
 */
class DataProviderPlugin
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
     * Retrieve calculate Reward Points amount for applying
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending\DataProvider $subject
     * @param int $customerId
     * @param int $websiteId
     * @param CartItemInterface[]|QuoteAbstractItem[] $quoteItemList
     * @param AddressInterface|Address $quoteAddress
     * @param int|null $pointsQtyToApply
     *
     * @return array
     */
    public function beforeGetData(
        $subject,
        $customerId,
        $websiteId,
        $quoteItemList,
        AddressInterface $quoteAddress,
        $pointsQtyToApply = null
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId, $quoteItemList, $quoteAddress, $pointsQtyToApply];
    }
}
