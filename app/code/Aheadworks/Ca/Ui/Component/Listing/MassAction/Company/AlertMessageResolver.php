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

namespace Aheadworks\Ca\Ui\Component\Listing\MassAction\Company;

use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Phrase;

/**
 * Class AlertMessageResolver
 */
class AlertMessageResolver
{
    /**
     * @var Manager
     */
    private Manager $thirdPartyModuleManager;

    /**
     * @param Manager $thirdPartyModuleManager
     */
    public function __construct(
        Manager $thirdPartyModuleManager
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * Retrieve message
     *
     * @return string
     */
    public function getMessage(): string
    {
        $message = '';
        $message .=  __('Are you sure to assign selected customers to the company?');
        if ($this->thirdPartyModuleManager->isAwCreditLimitModuleEnabled()) {
            $message .= '</br></br>' .
                __(
                    'These customers will see only Company Credit Limit on the frontend. ' .
                    'Customer\'s credit history can be viewed on the backend.'
                ) . '</br>';
        }
        if ($this->thirdPartyModuleManager->isAwNet30ModuleEnabled()) {
            $message .= '</br></br>' .
                __(
                    'Check if customer\'s have Net30 overdue payment'
                ) . '</br>';
        }

        return $message instanceof Phrase ? $message->render() : $message;
    }
}
