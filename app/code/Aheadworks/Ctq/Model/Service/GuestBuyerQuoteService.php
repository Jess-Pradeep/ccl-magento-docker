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
namespace Aheadworks\Ctq\Model\Service;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\GuestBuyerQuoteManagementInterface;

/**
 * Class GuestBuyerQuoteService
 *
 * @package Aheadworks\Ctq\Model\Service
 */
class GuestBuyerQuoteService implements GuestBuyerQuoteManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        BuyerQuoteManagementInterface $buyerQuoteManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * @inheritdoc
     */
    public function requestQuote($cartId, $requestInput)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $requestInput->setIsGuestQuote(true);

        return $this->buyerQuoteManagement->requestQuoteByRequest($quoteIdMask->getQuoteId(), $requestInput);
    }

    /**
     * @inheritdoc
     */
    public function requestQuoteList($cartId, $requestInput)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $requestInput->setIsGuestQuote(true);

        return $this->buyerQuoteManagement->requestQuoteListByRequest($quoteIdMask->getQuoteId(), $requestInput);
    }
}
