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
namespace Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateVariables;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ResetPasswordUrl
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateVariables
 */
class ResetPasswordUrl implements ModifierInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        /** @var CustomerInterface $customer */
        $customer = $relatedObjectList[ModifierInterface::CUSTOMER];
        $templateVariables[EmailVariables::RESET_PASSWORD_URL] = $this->url->getResetPasswordUrl($customer);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
