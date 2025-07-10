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

namespace Aheadworks\Ca\ViewModel\Company\Restriction;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\ShippingRestrictions\Model\ShippingManagementFactory;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AllowedShippingMethods
 */
class AllowedShippingMethods implements ArgumentInterface
{
    /**
     * @var ShippingManagementFactory
     */
    private ShippingManagementFactory $shippingManagementFactory;

    /**
     * @var Manager
     */
    private Manager $moduleManager;

    /**
     * @param ShippingManagementFactory $shippingManagementFactory
     * @param Manager $moduleManager
     */
    public function __construct(
        ShippingManagementFactory $shippingManagementFactory,
        Manager $moduleManager
    ) {
        $this->shippingManagementFactory = $shippingManagementFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get list of allowed shipping methods
     *
     * @return array
     */
    public function getList(): array
    {
        $shippingManagement = $this->shippingManagementFactory->create();
        return $shippingManagement->getShippingMethodsAsOptions();
    }

    /**
     * Check if section is active
     *
     * @return bool
     */
    public function isSectionActive(): bool
    {
        return $this->moduleManager->isAwShipRestModuleEnabled();
    }
}
