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

namespace Aheadworks\Ctq\Model\Data\Command\Quote\Save;

use Aheadworks\Ctq\Model\QuoteList\Item\CommentApplier;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Cart;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;

/**
 * Class Buyer
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote\Save
 */
class Buyer implements CommandInterface
{
    /**
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param RequestQuantityProcessor $quantityProcessor
     * @param CartRepositoryInterface $cartRepository
     * @param Cart $cart
     * @param CommentApplier $commentApplier
     */
    public function __construct(
        private readonly BuyerQuoteManagementInterface $buyerQuoteManagement,
        private readonly RequestQuantityProcessor $quantityProcessor,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly Cart $cart,
        private readonly CommentApplier $commentApplier
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        if (!isset($params['quote'])) {
            throw new \InvalidArgumentException('quote argument is required');
        }

        $data = $params['cart'];
        /** @var QuoteInterface $quote */
        $quote = $params['quote'];

        if (is_array($data)) {
            $cart = $this->cartRepository->get($quote->getCartId());
            $cart->setItems($cart->getAllVisibleItems());
            $cart->setAwCtqIsNotRequireValidation(true);
            $cart = $this->commentApplier->apply($cart);
            $this->cart->setQuote($cart);
            $data = $this->quantityProcessor->process($data);
            $data = $this->cart->suggestItemsQty($data);
            $this->cart->updateItems($data)->save();
        }
        $quote->setStatus(Status::PENDING_SELLER_REVIEW);
        $this->buyerQuoteManagement->updateQuote($quote);
        return $quote;
    }
}
