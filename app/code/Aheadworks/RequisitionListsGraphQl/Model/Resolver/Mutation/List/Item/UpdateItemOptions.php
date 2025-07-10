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
use Aheadworks\RequisitionListsGraphQl\Model\Data\DataProcessorInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\AbstractPrivateResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class UpdateItemOptions extends AbstractPrivateResolver
{
    /**
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param DataProcessorInterface $prepareDataProcessor
     * @param RequisitionListManagementInterface $requisitionListManagement
     */
    public function __construct(
        private readonly RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        private readonly RequisitionListRepositoryInterface $requisitionListRepository,
        private readonly DataProcessorInterface $prepareDataProcessor,
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
     * @throws ClientAware|LocalizedException
     */
    protected function performResolve(Field $field, ContextInterface $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $item = $result = $this->requisitionListItemRepository->get($args['item_id']);
        $list = $this->requisitionListRepository->get($item->getListId());
        $this->validateOwnership($list, $context);

        $updatedItemData = $this->prepareDataProcessor->prepareData($args['options']);

        if (!empty($args['options'])) {
            $result = $this->requisitionListManagement->updateItemOption((int)$args['item_id'], $updatedItemData);
        }

        return $result;
    }

    /**
     * Validate args
     *
     * @param array $args
     * @return bool|GraphQlInputException
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): bool|GraphQlInputException
    {
        if (!isset($args['item_id'])) {
            throw new GraphQlInputException(__('Required parameter "item_id" is missing.'));
        }

        if (!isset($args['options'])) {
            throw new GraphQlInputException(__('Required parameter "item_id" is missing.'));
        }

        return true;
    }
}
