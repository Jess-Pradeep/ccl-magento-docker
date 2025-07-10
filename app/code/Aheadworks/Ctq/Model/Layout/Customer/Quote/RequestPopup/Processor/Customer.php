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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Layout\Customer\Quote\RequestPopup\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ctq\Model\Layout\ProcessorInterface;

/**
 * Class Customer
 *
 * @package Aheadworks\Ctq\Model\Layout\Customer\Quote\RequestPopup\Processor
 */
class Customer implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param ArrayManager $arrayManager
     * @param CustomerSession $customerSession
     */
    public function __construct(
        ArrayManager $arrayManager,
        CustomerSession $customerSession
    ) {
        $this->arrayManager = $arrayManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $quoteProviderPath = $this->arrayManager->findPath('awCtqRequestQuoteProvider', $jsLayout);
        if ($quoteProviderPath) {
            $jsLayout = $this->arrayManager->merge(
                $quoteProviderPath,
                $jsLayout,
                [
                    'data' => [
                        'is_guest_quote' => !$this->customerSession->isLoggedIn()
                    ]
                ]
            );
        }

        return $jsLayout;
    }
}
