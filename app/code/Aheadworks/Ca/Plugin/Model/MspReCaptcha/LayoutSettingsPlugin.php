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
namespace Aheadworks\Ca\Plugin\Model\MspReCaptcha;

use Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Model\Config;
use Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier\MspReCaptcha;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class LayoutSettingsPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\MspReCaptcha
 */
class LayoutSettingsPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Config $config
     * @param Manager $manager
     */
    public function __construct(
        Config $config,
        Manager $manager
    ) {
        $this->config = $config;
        $this->manager = $manager;
    }

    /**
     * Adds company creation reCaptcha configuration parameter
     *
     * @param \MSP\ReCaptcha\Model\LayoutSettings $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCaptchaSettings($subject, array $result)
    {
        if ($this->manager->isMspReCaptchaModuleEnabled()) {
            $result['enabled'][MspReCaptcha::ZONE] = $this->config->isEnabledFrontendCompanyCreation();
        }

        return $result;
    }
}
