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
namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ResetPasswordUrl
 * @deprecated Refactoring required
 * @see \Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateVariables\ResetPasswordUrl
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class ResetPasswordUrl implements VariableProcessorInterface
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
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CustomerInterface $customer */
        $customer = $variables[EmailVariables::CUSTOMER];
        $variables[EmailVariables::RESET_PASSWORD_URL] = $this->url->getResetPasswordUrl($customer);

        return $variables;
    }
}
