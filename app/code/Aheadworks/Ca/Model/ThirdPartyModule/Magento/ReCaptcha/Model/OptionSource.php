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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager as ModuleManager;

/**
 * Class OptionSource
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Model
 */
class OptionSource implements OptionSourceInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->moduleManager->isMagentoReCaptchaCustomerModuleEnabled()) {
            /** @var OptionSourceInterface $optionSource */
            $optionSource = $this->objectManager->get(\Magento\ReCaptchaAdminUi\Model\OptionSource\Type::class);
            $options = $optionSource->toOptionArray();
        }

        return $options;
    }
}
