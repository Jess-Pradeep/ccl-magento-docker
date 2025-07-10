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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\Request\Validator;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\QuickOrder\Model\ProductList\SessionManager;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ItemId extends AbstractValidator
{
    /**
     * @param SessionManager $sessionManager
     * @param ProductListItemRepositoryInterface $productlistItemRepository
     */
    public function __construct(
        private SessionManager $sessionManager,
        private ProductListItemRepositoryInterface $productlistItemRepository
    ) {}

    /**
     * Check that the sent itemId belongs to the current customer
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function isValid($request): bool
    {
        $this->_clearMessages();

        $itemKey = $request->getParam(ProductListItemInterface::ITEM_KEY);
        if (!$itemKey) {
            $this->_addMessages([__('Product list item key is required')]);
        }

        $listId = $this->sessionManager->getActiveListIdForCurrentUser();
        if (!$listId) {
            $this->_addMessages([__('You don\'t have active list')]);
        }

        if ($itemKey && $listId) {
            try {
                $item = $this->productlistItemRepository->getByKey($itemKey);
                if ($item->getListId() != $listId) {
                    $this->_addMessages([__('You can\'t request for the given item.')]);
                }
            } catch (NoSuchEntityException $e) {
                $this->_addMessages([__($e->getMessage())]);
            }
        }

        return empty($this->getMessages());
    }
}
