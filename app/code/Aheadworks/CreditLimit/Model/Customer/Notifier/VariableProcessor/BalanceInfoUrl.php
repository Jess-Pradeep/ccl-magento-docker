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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor;

use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;
use Magento\Framework\Url as FrontendUrl;

/**
 * Class BalanceInfoUrl
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor
 */
class BalanceInfoUrl implements VariableProcessorInterface
{
    /**
     * @var FrontendUrl
     */
    private $url;

    /**
     * @param FrontendUrl $url
     */
    public function __construct(FrontendUrl $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        $variables[EmailVariables::BALANCE_INFO_URL] = $this->url->getUrl('aw_credit_limit/balance');
        return $variables;
    }
}
