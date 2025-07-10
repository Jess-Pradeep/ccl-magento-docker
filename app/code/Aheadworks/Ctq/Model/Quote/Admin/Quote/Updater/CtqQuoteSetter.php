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
namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ctq\Model\Quote\Discount\CurrencyRateConverter;

/**
 * Class CtqQuoteSetter
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater
 */
class CtqQuoteSetter
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CurrencyRateConverter
     */
    private $rateConverter;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CurrencyRateConverter $rateConverter
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        DataObjectHelper $dataObjectHelper,
        CurrencyRateConverter $rateConverter
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->rateConverter = $rateConverter;
    }

    /**
     * Attach aw ctq quote to cart using extension attributes
     *
     * @param CartInterface $cart
     * @param array $data
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function setAwCtqQuoteToCart($cart, $data)
    {
        $quoteData = $data['quote'] ?? null;
        if ($quoteData) {
            if (isset($quoteData['quote_id'])) {
                $quote = $this->quoteRepository->get($quoteData['quote_id']);
            } else {
                /** @var QuoteInterface $quote */
                $quote = $this->quoteFactory->create();
            }

            if (isset($quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_TYPE])
                && isset($quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_VALUE])) {
                $value = $this->rateConverter->convertAmountValueToCurrency(
                    $quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_TYPE],
                    $quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_VALUE],
                    $cart->getCurrency()->getQuoteCurrencyCode(),
                    $cart->getCurrency()->getBaseCurrencyCode()
                );
                $quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_VALUE] = $value;
            } elseif (isset($quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_TYPE])) {
                $quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_TYPE] = null;
                $quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_VALUE] = null;
            }

            $this->dataObjectHelper->populateWithArray(
                $quote,
                $quoteData,
                QuoteInterface::class
            );

            $extensionAttributes = $cart->getExtensionAttributes()
                ? $cart->getExtensionAttributes()
                : $this->cartExtensionFactory->create();
            $extensionAttributes->setAwCtqQuote($quote);
            $cart->setExtensionAttributes($extensionAttributes);
        }
    }
}
