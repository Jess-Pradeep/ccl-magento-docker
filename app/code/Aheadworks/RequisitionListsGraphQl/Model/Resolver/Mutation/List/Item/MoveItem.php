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
 * @package    RequisitionListsGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionListsGraphQl\Model\Resolver\Mutation\List\Item;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\AbstractPrivateResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class MoveItem extends AbstractPrivateResolver
{
    /**
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param RequisitionListManagementInterface $requisitionListManagement
     */
    public function __construct(
        private readonly RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        private readonly RequisitionListRepositoryInterface $requisitionListRepository,
        private readonly RequisitionListManagementInterface $requisitionListManagement
    ) {
    }

    /**
     * Perform resolve method after validate customer authorization
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return RequisitionListItemInterface
     * @throws AggregateExceptionInterface
     * @throws ClientAware
     */
    protected function performResolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        try {
            $listToMove = $this->requisitionListRepository->get($args['list_id_to_move']);
            $item = $this->requisitionListItemRepository->get($args['item_id']);
            $listFromItem = $this->requisitionListRepository->get($item->getListId());

            $this->validateOwnership($listToMove, $context);
            $this->validateOwnership($listFromItem, $context);

            $result = $this->requisitionListManagement->moveItem($item, (int)$listToMove->getListId());

        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $result;
    }

    /**
     * Validate requested arguments
     *
     * @param array $args
     * @return bool|GraphQlInputException
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): bool|GraphQlInputException
    {
        if (empty($args['item_id'])) {
            throw new GraphQlInputException(__('Required parameter "item_id" is missing.'));
        }

        if (empty($args['list_id_to_move'])) {
            throw new GraphQlInputException(__('Required parameter "item" is missing.'));
        }

        return true;
    }
}
