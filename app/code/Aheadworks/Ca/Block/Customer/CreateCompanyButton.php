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
namespace Aheadworks\Ca\Block\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Ca\Model\Url;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Source\Customer\RegistrationType;

/**
 * Class CreateCompanyButton
 *
 * @package Aheadworks\Ca\Block\Customer
 */
class CreateCompanyButton extends Template
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Return company create page url
     *
     * @return string
     */
    public function getCreatePageUrl()
    {
        return $this->url->getFrontendCreateCompanyFormUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _toHtml(): string
    {
        $websiteId = (int)$this->_storeManager->getWebsite()->getId();
        $isRegistrationAllowed = $this->config->isRegistrationOnFrontendEnabled(RegistrationType::COMPANY, $websiteId);
        $isModuleEnabled = $this->config->isExtensionEnabled($websiteId);

        if (!$isModuleEnabled || !$isRegistrationAllowed) {
            return '';
        }

        return parent::_toHtml();
    }
}
