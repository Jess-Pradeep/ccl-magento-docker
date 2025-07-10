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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Discount\CurrencyRateConverter;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;

/**
 * Class NegotiatedDiscount
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor
 */
class NegotiatedDiscount implements ProcessorInterface
{
    /**
     * @var CurrencyRateConverter
     */
    private $rateConverter;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @param CurrencyRateConverter $rateConverter
     * @param QuoteSession $quoteSession
     */
    public function __construct(
        CurrencyRateConverter $rateConverter,
        QuoteSession $quoteSession
    ) {
        $this->rateConverter = $rateConverter;
        $this->quoteSession = $quoteSession;
    }

    /**
     * Convert negotiated discount amount to base amount
     *
     * Works in backend only in case currency is changed
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function process($data)
    {
        $quote = $this->quoteSession->getQuote();
        if ($quote && isset($data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_TYPE])) {
            $value = $this->rateConverter->convertAmountValueToCurrency(
                $data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_TYPE],
                $data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_VALUE],
                $quote->getQuoteCurrencyCode(),
                $quote->getBaseCurrencyCode()
            );
            $data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_VALUE] = $value;
        } elseif (isset($data['quote'])) {
            $data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_TYPE] = null;
            $data['quote'][QuoteInterface::NEGOTIATED_DISCOUNT_VALUE] = null;
        }

        return $data;
    }
}
