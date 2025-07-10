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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Model;

use Aheadworks\ShippingRestrictions\Model\ShippingManagement;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ShippingManagementFactory
 */
class ShippingManagementFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create shipping restriction payment management instance
     *
     * @return ShippingManagement
     */
    public function create(): ShippingManagement
    {
        return $this->objectManager->get(ShippingManagement::class);
    }
}
