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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class DeleteItem
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class DeleteItem extends AbstractUpdateItemAction
{
    /**
     * {@inheritdoc}
     */
    protected function update($resultRedirect)
    {
        $items = $this->getItems();
        if ($items) {
            try {
                /** @var RequisitionListItemInterface $item */
                foreach ($items as $item) {
                    $this->requisitionListItemRepository->delete($item);
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        '%1 item(s) have been successfully removed from "%2".',
                        count($items),
                        $this->getCurrentRequisitionListName()
                    )
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete Item right now.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while delete Item.')
            );
        }

        return $resultRedirect;
    }

    /**
     * {@inheritDoc}
     */
    protected function getItems()
    {
        if ($itemId = $this->getRequest()->getParam('item_id', null)) {
            try {
                return [$this->requisitionListItemRepository->get($itemId)];
            } catch (\Exception $e) {
                return [];
            }
        } else {
            return parent::getItems();
        }
    }
}
