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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class CreateAwCaCompanyObserver
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Observer
 */
class CreateAwCaCompanyObserver implements ObserverInterface
{
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
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleManager->isMspReCaptchaModuleEnabled()) {
            /** @var ObserverInterface $observerInstance */
            $observerInstance = $this->objectManager->get(
                \Aheadworks\Ca\Model\ThirdPartyModule\Msp\ReCaptcha\Observer\Frontend\CreateAwCaCompanyObserver::class
            );

            $observerInstance->execute($observer);
        }
    }
}
