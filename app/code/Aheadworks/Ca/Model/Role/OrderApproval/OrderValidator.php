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
use Aheadworks\Ca\Model\ResourceModel\Role\OrderApproval\State as StateResourceModel;

/**
 * Class OrderValidator
 *
 * @package Aheadworks\Ca\Model\Role\OrderApproval
 */
class OrderValidator
{
    /**#@+
     * Restriction list
     */
    const TO_APPLY_APPROVAL = 'to_apply_approval';
    const TO_APPROVE = 'to_approve';
    const TO_REJECT = 'to_reject';
    /**#@-*/

    /**
     * @var StateResourceModel
     */
    private $stateResourceModel;

    /**
     * @var string[]
     */
    private $messages;

    /**
     * @var array
     */
    private $restrictions;

    /**
     * @param StateResourceModel $stateResourceModel
     * @param array $restrictions
     */
    public function __construct(
        StateResourceModel $stateResourceModel,
        array $restrictions
    ) {
        $this->stateResourceModel = $stateResourceModel;
        $this->restrictions = $restrictions;
    }

    /**
     * Validate order to apply approval
     *
     * @param OrderInterface $order
     * @return bool
     * @throws LocalizedException
     */
    public function validateToApplyApproval(OrderInterface $order)
    {
        $this->clearMessages();
        $stateId = $this->stateResourceModel->getStateIdByOrderId($order->getEntityId());
        if ($stateId) {
            $this->messages[] = __('Order approval is already applied for order: %1', $order->getEntityId());
            return false;
        }

        return $this->checkRestrictions(self::TO_APPLY_APPROVAL, $order->getStatus());
    }

    /**
     * Validate order to approve
     *
     * @param OrderInterface $order
     * @return bool
     * @throws LocalizedException
     */
    public function validateToApprove(OrderInterface $order)
    {
        $this->clearMessages();
        $stateId = $this->stateResourceModel->getStateIdByOrderId($order->getEntityId());
        if (!$stateId) {
            $this->messages[] = __('Order: %1 cannot be approved at the moment', $order->getEntityId());
            return false;
        }

        return $this->checkRestrictions(self::TO_APPROVE, $order->getStatus());
    }

    /**
     * Validate order to reject
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function validateToReject(OrderInterface $order)
    {
        $this->clearMessages();
        return $this->checkRestrictions(self::TO_REJECT, $order->getStatus());
    }

    /**
     * Get messages
     *
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages ?? [];
    }

    /**
     * Clear messages
     */
    private function clearMessages()
    {
        $this->messages = [];
    }

    /**
     * Check restrictions
     *
     * @param string $restriction
     * @param string $status
     * @return bool
     */
    private function checkRestrictions($restriction, $status)
    {
        $restrictions = $this->restrictions[$restriction];
        $message = __('Status order %1 is not allowed. Restriction: %2 ', $status, $restriction);
        if (!empty($restrictions['statuses_to_allow'])) {
            $isFound = false;
            foreach ($restrictions['statuses_to_allow'] as $statusToAllow) {
                if ($statusToAllow == $status) {
                    $isFound = true;
                    break;
                }
            }

            if (!$isFound) {
                $this->messages[] = $message;
                return false;
            }
        }

        if (!empty($restrictions['statuses_to_forbid'])) {
            foreach ($restrictions['statuses_to_forbid'] as $statusToDisallow) {
                if ($statusToDisallow == $status) {
                    $this->messages[] = $message;
                    return false;
                }
            }
        }

        return true;
    }
}
