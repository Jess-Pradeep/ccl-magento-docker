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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Third party manager
 */
class Manager
{
    /**
     * Aheadworks Company Accounts module name
     */
    public const AW_CA_MODULE_NAME = 'Aheadworks_Ca';

    /**
     * Manager constructor.
     *
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        private ModuleListInterface $moduleList
    ) {
    }

    /**
     * Check if Aheadworks Company Accounts module enabled
     *
     * @return bool
     */
    public function isAwCaModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_CA_MODULE_NAME);
    }
}
