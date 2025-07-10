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

use Aheadworks\RequisitionLists\Api\CartManagementInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\AbstractPrivateResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class AddToCart extends AbstractPrivateResolver
{
    /**
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param CartManagementInterface $cartManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     */
    public function __construct(
        private readonly RequisitionListRepositoryInterface $requisitionListRepository,
        private readonly RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        private readonly CartManagementInterface $cartManagement,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
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
     * @return int
     * @throws AggregateExceptionInterface
     * @throws ClientAware|LocalizedException
     */
    protected function performResolve(Field $field, ContextInterface $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $list = $this->requisitionListRepository->get($args['list_id']);
        $this->validateOwnership($list, $context);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(RequisitionListItemInterface::LIST_ID, $list->getListId())
            ->addFilter(RequisitionListItemInterface::ITEM_ID, $args['item_ids'], 'in')
            ->create();

        $items = $this->requisitionListItemRepository->getList($searchCriteria)->getItems();
        $cartId = $this->maskedQuoteIdToQuoteId->execute($args['cart_id']);

        return $this->cartManagement->addItemsToCart($items, $cartId);
    }

    /**
     * Validate arguments
     *
     * @param array $args
     * @return GraphQlInputException|bool
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): GraphQlInputException|bool
    {
        if (empty($args['item_ids'])) {
            throw new GraphQlInputException(__('Required parameter "item_ids" is missing.'));
        }

        if (empty($args['list_id'])) {
            throw new GraphQlInputException(__('Required parameter "list_id" is missing.'));
        }

        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing.'));
        }

        return true;
    }
}
