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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Model\Config as ReCaptchaConfig;

/**
 * Class MspReCaptcha
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class MagentoReCaptcha implements ModifierInterface
{
    const RE_CAPTCHA_FOR = 'aw_ca_company_creation';

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var ReCaptchaConfig
     */
    private $reCaptchaConfig;

    /**
     * @param ArrayManager $arrayManager
     * @param JsonSerializer $jsonSerializer
     * @param LayoutInterface $layout
     * @param Manager $moduleManager
     * @param ReCaptchaConfig $reCaptchaConfig
     */
    public function __construct(
        ArrayManager $arrayManager,
        JsonSerializer $jsonSerializer,
        LayoutInterface $layout,
        Manager $moduleManager,
        ReCaptchaConfig $reCaptchaConfig
    ) {
        $this->arrayManager = $arrayManager;
        $this->jsonSerializer = $jsonSerializer;
        $this->moduleManager = $moduleManager;
        $this->layout = $layout;
        $this->reCaptchaConfig = $reCaptchaConfig;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function modifyMeta(array $meta)
    {
        if (!($this->moduleManager->isMagentoReCaptchaCustomerModuleEnabled()
            && $this->reCaptchaConfig->isEnabledFrontendCompanyCreation())
        ) {
            return $meta;
        }

        $captchaBlock = $this->getMagentoReCaptchaFrontendBlock();
        $mspCaptchaLayout = $this->jsonSerializer->unserialize($captchaBlock->getJsLayout());
        $formPath = $this->arrayManager->findPath('extra_form', $meta);
        $meta = $this->arrayManager->merge($formPath . '/children', $meta, $mspCaptchaLayout['components']);

        $formInitializerPath = $this->arrayManager->findPath('form_initializer', $meta);
        $initializerDeps = $this->arrayManager->get($formInitializerPath . '/deps', $meta);
        $captchaComponent = reset($mspCaptchaLayout['components']);
        $initializerDeps[] = 'awCaForm.extra_form.' . $captchaComponent['reCaptchaId'];
        $meta = $this->arrayManager->set($formInitializerPath . '/deps', $meta, $initializerDeps);

        return $meta;
    }

    /**
     * Retrieve Magento ReCaptcha frontend block
     *
     * @return \Magento\ReCaptchaUi\Block\ReCaptcha|BlockInterface
     */
    private function getMagentoReCaptchaFrontendBlock()
    {
        $arguments = [
            'data' => [
                'jsLayout' => [
                    'components' => [
                        'recaptcha' => [
                            'component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha'
                        ]
                    ]
                ],
                'recaptcha_for' => self::RE_CAPTCHA_FOR
            ]
        ];

        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->layout->createBlock(
            \Magento\ReCaptchaUi\Block\ReCaptcha::class,
            'recaptcha',
            $arguments
        );
        $block->setTemplate('Magento_ReCaptchaFrontendUi::recaptcha.phtml');

        return $block;
    }
}
