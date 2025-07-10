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
declare(strict_types=1);

namespace Aheadworks\Ca\Block\Customer;

use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Source\Customer\RegistrationType;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Link as HtmlLink;
use Magento\Framework\View\Element\Template\Context;

class CreateCompanyTopLink extends HtmlLink
{
    /**
     * @param Context $context
     * @param HttpContext $httpContext
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly HttpContext $httpContext,
        private readonly Url $url,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref(): string
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
        if ($this->isModuleEnabled()
            && !$this->isRegistrationAllowed(RegistrationType::CUSTOMER)
            && $this->isRegistrationAllowed(RegistrationType::COMPANY)
            && !$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)
        ) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Check if registration is allowed
     *
     * @param string $type
     * @return bool
     * @throws LocalizedException
     */
    private function isRegistrationAllowed(string $type): bool
    {
        return $this->config->isRegistrationOnFrontendEnabled(
            $type,
            $this->_storeManager->getWebsite()->getId()
        );
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     * @throws LocalizedException
     */
    private function isModuleEnabled(): bool
    {
        return $this->config->isExtensionEnabled((int)$this->_storeManager->getWebsite()->getId());
    }
}
