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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Config
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Model
 */
class Config
{
    /**
     * Enables reCaptcha on company creation form
     */
    const XML_PATH_ENABLED_FRONTEND_AW_CA_COMPANY_CREATION
        = 'msp_securitysuite_recaptcha/frontend/enabled_aw_ca_company_creation';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * Return true if enabled on frontend company creation form
     *
     * @return bool
     */
    public function isEnabledFrontendCompanyCreation()
    {
        /** @var \MSP\ReCaptcha\Model\Config $reCaptchaConfig */
        $reCaptchaConfig = $this->objectManager->get(\MSP\ReCaptcha\Model\Config::class);
        if (!$reCaptchaConfig->isEnabledFrontend()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(static::XML_PATH_ENABLED_FRONTEND_AW_CA_COMPANY_CREATION);
    }
}
