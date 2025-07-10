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

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class Config
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Model
 */
class Config
{
    const RE_CAPTCHA_FOR = 'aw_ca_company_creation';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Return true if enabled on frontend company creation form
     *
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     */
    public function isEnabledFrontendCompanyCreation()
    {
        if ($this->moduleManager->isMagentoReCaptchaCustomerModuleEnabled()) {
            /** @var \Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface $config */
            $config = $this->objectManager->get(\Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface::class);
            return $config->isCaptchaEnabledFor(self::RE_CAPTCHA_FOR);
        }

        return false;
    }
}
