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

namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Model\ShippingManagementFactory;

/**
 * Shipping method source
 */
class ShippingMethod implements OptionSourceInterface
{
    /**
     * @var ShippingManagementFactory
     */
    private ShippingManagementFactory $shippingManagementFactory;

    /**
     * @var Manager
     */
    private Manager $thirdPartyModuleManager;

    /**
     * @param ShippingManagementFactory $shippingManagementFactory
     * @param Manager $thirdPartyModuleManager
     */
    public function __construct(
        ShippingManagementFactory $shippingManagementFactory,
        Manager $thirdPartyModuleManager
    ) {
        $this->shippingManagementFactory = $shippingManagementFactory;
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        if ($this->thirdPartyModuleManager->isAwShipRestModuleEnabled()) {
            $shippingManagement = $this->shippingManagementFactory->create();
            $options = $shippingManagement->getShippingMethodsAsOptions();
        }

        return $options;
    }
}
