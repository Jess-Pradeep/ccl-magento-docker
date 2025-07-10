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
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Carrier;

use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Checker
{
    /**
     * @param BackendSession $backendSession
     * @param CartRepositoryInterface $quoteRepository
     * @param RequestInterface $request
     * @param array $excludedActionName
     */
    public function __construct(
        private readonly BackendSession $backendSession,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly RequestInterface $request,
        private readonly array $excludedActionName = []
    ) {}

    /**
     * Check if can add custom rate
     *
     * @param RateRequest $request
     * @return bool
     * @throws NoSuchEntityException
     */
    public function canAddCustomRate(RateRequest $request): bool
    {
        $result = true;

        $backendUser = $this->backendSession->getUser();
        if (!$backendUser || !$backendUser->getId()) {
            foreach ($request->getAllItems() ?? [] as $item) {
                $quoteId = $item->getQuoteId();
                if ($quoteId) {
                    $quote = $this->quoteRepository->get($quoteId);
                    break;
                }
            }
            try {
                $extensionAttributes = $quote->getExtensionAttributes();
                $shippingAddress = $extensionAttributes->getAwCtqQuote()->getCart()->getShippingAddress();
                if ($quote->getAwCtqIsNotRequireValidation()) {
                    return $result;
                }
                if ($shippingAddress['shipping_method'] !== Custom::CUSTOM_CARRIER . '_' . Custom::CUSTOM_CARRIER) {
                    $result = false;
                }
            } catch (\Throwable $e) {
                $result = false;
            }
        }
        if (in_array($this->request->getFullActionName(), $this->excludedActionName, false)) {
            $result = false;
        }

        return $result;
    }
}

