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
namespace Aheadworks\Ca\Plugin\Model\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Registration;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Source\Customer\RegistrationType;

/**
 * Class RegistrationPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\Customer
 */
class RegistrationPlugin
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Check if customer registration is allowed
     *
     * @param Registration $subject
     * @param boolean $result
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowed($subject, $result)
    {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isModuleEnabled = $this->config->isExtensionEnabled($websiteId);
        $isRegistrationAllowed = $this->config->isRegistrationOnFrontendEnabled(RegistrationType::CUSTOMER, $websiteId);

        if ($isModuleEnabled) {
            $result = $isRegistrationAllowed;
        }

        return $result;
    }
}
