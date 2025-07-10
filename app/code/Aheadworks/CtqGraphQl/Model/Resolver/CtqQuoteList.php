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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\Resolver;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\CtqGraphQl\Model\ObjectConverter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CtqQuoteList extends AbstractResolver
{
    /**
     * @param QuoteRepositoryInterface $repository
     * @param ObjectConverter $objectConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly ObjectConverter $objectConverter,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder,
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
     * @return mixed
     * @throws AggregateExceptionInterface
     * @throws GraphQlAuthorizationException
     * @throws LocalizedException
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->validateArgs($args);
        if (!$this->validateCustomer($args['customerId'], $context)) {
            throw new GraphQlAuthorizationException(
                __(
                    'The current customer id="%customerId" cannot perform operations on quote',
                    ['customerId' => $args['customerId']]
                )
            );
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField(QuoteInterface::CREATED_AT)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        $this->searchCriteriaBuilder
            ->addFilter(QuoteInterface::CUSTOMER_ID, ['eq' => $args['customerId']])
            ->addSortOrder($sortOrder);
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $quoteList = $this->repository->getList($this->searchCriteriaBuilder->create(), $storeId)->getItems();

        return $this->objectConverter->convertToArray($quoteList, QuoteInterface::class);
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
        if (isset($args['customerId']) && $args['customerId'] < 1) {
            throw new GraphQlInputException(__('Specify the "customerId" value.'));
        }

        return true;
    }
}
