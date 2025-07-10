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

namespace Aheadworks\RequisitionListsGraphQl\Model\Resolver;

use Aheadworks\RequisitionListsGraphQl\Model\DataProveder\DataProviderInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\Argument\Modifier\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AbstractEntityListResolver extends AbstractPrivateResolver
{
    /**
     * @param Builder $searchCriteriaBuilder
     * @param ModifierInterface|null $argsModifier
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(
        private readonly Builder $searchCriteriaBuilder,
        private readonly ?ModifierInterface $argsModifier,
        private readonly DataProviderInterface $dataProvider
    ) {
    }

    /**
     * Perform Resolve
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function performResolve(
        Field $field,
        ContextInterface $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if ($this->argsModifier) {
            $args = $this->argsModifier->modifyArgs($field, $context, $info, $args);
        }

        $searchCriteria = $this->searchCriteriaBuilder->build($field->getName(), $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $searchResult = $this->dataProvider->getListData($searchCriteria);

        $data = [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getItems(),
            'page_info' => [
                'page_size' => $searchCriteria->getPageSize(),
                'current_page' => $this->resolveCurrentPage($searchCriteria, $searchResult)
            ]
        ];

        return $data;
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
        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('`currentPage` value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('`pageSize` value must be greater than 0.'));
        }

        return true;
    }

    /**
     * Resolve current page
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchResultsInterface $searchResult
     * @return GraphQlInputException
     */
    private function resolveCurrentPage($searchCriteria, $searchResult)
    {
        $maxPages = $searchCriteria->getPageSize()
            ? ceil($searchResult->getTotalCount() / $searchCriteria->getPageSize())
            : 0;

        $currentPage = $searchCriteria->getCurrentPage();
        if ($searchCriteria->getCurrentPage() > $maxPages && $searchResult->getTotalCount() > 0) {
            $currentPage = new GraphQlInputException(
                __('currentPage value %1 specified is greater than the number of pages available.', $maxPages)
            );
        }
        return $currentPage;
    }
}
