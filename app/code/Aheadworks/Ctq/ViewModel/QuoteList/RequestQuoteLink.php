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
namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\ViewModel\Checkout\RequestQuoteLink as CheckoutRequestQuoteLink;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class RequestQuoteLink extends CheckoutRequestQuoteLink implements ArgumentInterface
{
    /**
     * {@inheritDoc}
     */
    public function isRequestQuoteButtonAvailable()
    {
        $cartId = $this->checkoutSession->getAwCtqQuoteListId();
        return $this->buyerPermissionManagement->canShowRequestQuoteButtonQuoteList($cartId);
    }

    /**
     * @inheritDoc
     */
    public function isRequestQuoteAvailable()
    {
        return true;
    }

    /**
     * Get curret quote id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->checkoutSession->getAwCtqQuoteListId();
    }
}
