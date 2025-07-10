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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Plugin\Model;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Model\ShippingManagement;
use Aheadworks\ShippingRestrictions\Model\ShippingManagement as ShipRestShippingManagement;

/**
 * Class ShippingManagementPlugin
 */
class ShippingManagementPlugin
{
    /**
     * @var ShippingManagement
     */
    private ShippingManagement $shippingManagement;

    /**
     * @param ShippingManagement $shippingManagement
     */
    public function __construct(ShippingManagement $shippingManagement)
    {
        $this->shippingManagement = $shippingManagement;
    }

    /**
     * Is available by method code
     *
     * @param ShipRestShippingManagement $subject
     * @param callable $proceed
     * @param string $shippingCode
     * @param int|null $group
     * @param int|null $websiteId
     * @return bool
     */
    public function aroundIsAvailable(
        ShipRestShippingManagement $subject,
        callable $proceed,
        string $shippingCode,
        ?int $group = null,
        ?int $websiteId = null
    ): bool {
        $allowedShippingMethods = $this->shippingManagement->getAllowedCompanyShippingMethods();

        return !empty($allowedShippingMethods)
            ? in_array($shippingCode, $allowedShippingMethods)
            : $proceed($shippingCode, $group, $websiteId);
    }
}
