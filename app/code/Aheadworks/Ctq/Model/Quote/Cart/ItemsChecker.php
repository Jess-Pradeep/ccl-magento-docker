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
namespace Aheadworks\Ctq\Model\Quote\Cart;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ItemsChecker
 */
class ItemsChecker
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * ItemsChecker constructor.
     * @param StoreManagerInterface $storeManager
     * @param QuoteManagement $quoteManagement
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        QuoteManagement $quoteManagement
    ) {
        $this->storeManager = $storeManager;
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Check items in cart to quote
     *
     * @param QuoteInterface $quote
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkData($quote)
    {
        $failItems = [];
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        $cart = $this->quoteManagement->getCartByQuoteId($quote->getId());

        foreach ($cart->getItemsCollection() as $item) {
            if (!is_array($item->getProduct()->getWebsiteIds())
                || !in_array($currentWebsiteId, $item->getProduct()->getWebsiteIds())) {
                $failItems[] = $item->getName();
            }
        }

        return $failItems;
    }
}