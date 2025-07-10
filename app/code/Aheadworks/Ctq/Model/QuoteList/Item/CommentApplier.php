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

namespace Aheadworks\Ctq\Model\QuoteList\Item;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;

class CommentApplier
{
    /**
     * @param RequestInterface $request
     */
    public function __construct(private readonly RequestInterface $request) {}

    /**
     * Add item comment to quote items
     *
     * @param CartInterface|null $quote
     * @return CartInterface|null
     */
    public function apply(CartInterface|null $quote): ?CartInterface
    {
        if (!$quote) {
            return $quote;
        }
        $itemComments = $this->request->getParam('aw_ctq_quote_item_comment');
        if ($itemComments) {
            foreach ($itemComments as $itemId => $comment) {
                foreach ($quote->getItems() as $item) {
                    if ((int)$item->getItemId() === $itemId) {
                        $item->setAwCtqItemComment($comment);
                    }
                }
            }
        }

        return $quote;
    }

    /**
     * Add item comment to item
     *
     * @param Item $item
     * @return Item
     */
    public function applyByItem(Item $item): Item
    {
        $itemComments = $this->request->getParam('aw_ctq_quote_item_comment');
        if ($itemComments) {
            foreach ($itemComments as $itemId => $comment) {
                if ((int)$item->getItemId() === $itemId) {
                    $item->setAwCtqItemComment($comment);
                }
            }
        }

        return $item;
    }
}
