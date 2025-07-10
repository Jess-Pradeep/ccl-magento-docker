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
namespace Aheadworks\Ca\Model\Source\Role\OrderApproval;

/**
 * Class OrderStatus
 *
 * @package Aheadworks\Ca\Model\Source\Role\OrderApproval
 */
class OrderStatus
{
    /**#@+
     * Order custom statuses
     */
    const PENDING_APPROVAL = 'aw_ca_pending_approval';
    const REJECTED = 'aw_ca_rejected';
    /**#@-*/

    /**
     * Get pending status data
     *
     * @return array
     */
    public function getPendingStatus()
    {
        return [
            'status' => self::PENDING_APPROVAL,
            'label' => __('Company Pending Approval')
        ];
    }

    /**
     * Get rejected status data
     *
     * @return array
     */
    public function getRejectedStatus()
    {
        return [
            'status' => self::REJECTED,
            'label' => __('Rejected by Company Admin')
        ];
    }
}
