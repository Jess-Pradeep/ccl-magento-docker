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
namespace Aheadworks\Ctq\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class ModuleChecker
 * @package Aheadworks\Ctq\Model\ThirdPartyModule
 */
class ModuleChecker
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Aheadworks Company Account module enabled
     *
     * @return bool
     */
    public function isAwCaEnabled()
    {
        return $this->moduleList->has('Aheadworks_Ca');
    }

    /**
     * Check if Magento Persistent module enabled
     *
     * @return bool
     */
    public function isMagentoPersistentEnabled()
    {
        return $this->moduleList->has('Magento_Persistent');
    }
}
