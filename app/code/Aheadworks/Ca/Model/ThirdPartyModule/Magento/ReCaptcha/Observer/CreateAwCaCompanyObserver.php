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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Action\Action;
use Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Model\Config;
use Aheadworks\Ca\Model\Url;

/**
 * Class CreateAwCaCompanyObserver
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\ReCaptcha\Observer
 */
class CreateAwCaCompanyObserver implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     * @param Url $url
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        Url $url
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabledFrontendCompanyCreation()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            /** @var \Magento\ReCaptchaUi\Model\RequestHandlerInterface $requestHandler */
            $requestHandler = $this->objectManager->get(
                \Magento\ReCaptchaUi\Model\RequestHandlerInterface::class
            );

            $requestHandler->execute(
                Config::RE_CAPTCHA_FOR,
                $controller->getRequest(),
                $controller->getResponse(),
                $this->url->getFrontendCreateCompanyFormUrl()
            );
        }
    }
}
