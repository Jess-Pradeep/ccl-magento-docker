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

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\Source\History\Action\Type as ActionType;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;

class ItemsCommentBuilder implements BuilderInterface
{
    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     */
    public function __construct(private readonly HistoryActionInterfaceFactory $historyActionFactory)
    {
    }

    /**
     * Build history action from quote object
     *
     * @param QuoteInterface|Quote $quote
     * @return HistoryActionInterface[]
     */
    public function build($quote)
    {
        $historyActions = [];
        $newItems = $quote->getCart()->getItems();

        if ($quote->getOrigData(QuoteInterface::ID)) {
            /** @var QuoteCartInterface $oldCart */
            $oldCart = $quote->getOrigData(QuoteInterface::CART);
            $oldItems = $oldCart ? $oldCart->getItems() : [];

            foreach ($oldItems as $oldItem) {
                $oldItemId = $oldItem['item_id'];

                $newItem = $this->getItemById($oldItemId, $newItems);
                if ($newItem) {
                    $oldComment = $oldItem['aw_ctq_item_comment'] ?? null;
                    $newComment = $newItem['aw_ctq_item_comment'] ?? null;

                    if ($oldComment === $newComment) {
                        continue;
                    }

                    $historyAction = $this->historyActionFactory->create();
                    $historyAction
                        ->setType(ActionType::PRODUCT_COMMENT)
                        ->setStatus(ActionStatus::UPDATED)
                        ->setOldValue($oldComment)
                        ->setValue($newComment);
                    $historyActions[] = $historyAction;
                }
            }

            return $historyActions;
        }

        foreach ($newItems as $item) {
            if ($item['aw_ctq_item_comment']) {
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(ActionType::PRODUCT_COMMENT)
                    ->setStatus(ActionStatus::CREATED)
                    ->setValue($item['aw_ctq_item_comment']);
                $historyActions[] = $historyAction;
            }
        }

        return $historyActions;
    }

    /**
     * Retrieve item by id
     *
     * @param int $itemId
     * @param array $items
     * @param string $fieldName
     * @param bool $withItemKey
     * @return array|null
     */
    private function getItemById($itemId, $items, $fieldName = 'item_id', $withItemKey = false)
    {
        foreach ($items as $itemKey => $item) {
            if ($itemId == $item[$fieldName]) {
                return $withItemKey ? [$itemKey, $item] : $item;
            }
        }

        return null;
    }
}
