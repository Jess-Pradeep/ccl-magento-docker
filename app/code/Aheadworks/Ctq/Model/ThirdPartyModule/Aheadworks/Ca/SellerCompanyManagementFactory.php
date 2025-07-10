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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca;

use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ctq\Model\ThirdPartyModule\ModuleChecker;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class SellerCompanyManagementFactory
 * @package Aheadworks\Ctq\Model\ThirdPartyModule\Aheadworks\Ca
 */
class SellerCompanyManagementFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleChecker $moduleChecker
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleChecker $moduleChecker
    ) {
        $this->objectManager = $objectManager;
        $this->moduleChecker = $moduleChecker;
    }

    /**
     * Create seller company management
     *
     * @return SellerCompanyManagementInterface|null
     */
    public function create()
    {
        return $this->moduleChecker->isAwCaEnabled()
            ? $this->objectManager->get(SellerCompanyManagementInterface::class)
            : null;
    }
}
