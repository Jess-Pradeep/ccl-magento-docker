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

use Aheadworks\Ctq\Model\Quote\Discount\CurrencyRateConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class Resolver
 */
class Resolver
{
    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param CurrencyRateConverter $rateConverter
     */
    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly CurrencyRateConverter $rateConverter
    ) {}

    /**
     * Get current amount
     *
     * @param RateRequest $request
     * @return float
     * @throws NoSuchEntityException
     */
    public function getCurrentAmount(RateRequest $request): float
    {
        foreach ($request->getAllItems() ?? [] as $item) {
            $quoteId = $item->getQuoteId();
            if ($quoteId) {
                $quote = $this->quoteRepository->get($quoteId);
                break;
            }
        }
        $extensionAttributes = $quote->getExtensionAttributes();
        $shippingAddress = $extensionAttributes->getAwCtqQuote()->getCart()->getShippingAddress();
        if (isset($shippingAddress['shipping_method']) &&
            $shippingAddress['shipping_method'] !== Custom::CUSTOM_CARRIER . '_' . Custom::CUSTOM_CARRIER) {
            return 0.0;
        }
        $value = $this->rateConverter->convertAmountValueToCurrency(
            'amount',
            $shippingAddress['shipping_amount'],
            $quote->getQuoteCurrencyCode(),
            $quote->getBaseCurrencyCode()
        );

        return $value;
    }
}
