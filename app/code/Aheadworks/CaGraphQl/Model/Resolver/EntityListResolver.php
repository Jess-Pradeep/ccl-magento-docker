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
 * @package    CaGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CaGraphQl\Model\Resolver;

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\CaGraphQl\Model\Resolver\Argument\Modifier\ModifierInterface;
use Aheadworks\CaGraphQl\Model\Resolver\DataProvider\DataProviderInterface;
use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class EntityListResolver implements ResolverInterface
{
    /**
     * @param Builder $searchCriteriaBuilder
     * @param DataProviderInterface $dataProvider
     * @param CompanyUserProvider $companyUserProvider
     * @param bool $isCompanyRequired
     * @param ModifierInterface|null $argsModifier
     */
    public function __construct(
        private readonly Builder $searchCriteriaBuilder,
        private readonly DataProviderInterface $dataProvider,
        private readonly CompanyUserProvider $companyUserProvider,
        private readonly bool $isCompanyRequired = true,
        private readonly ?ModifierInterface $argsModifier = null
    ) {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws Exception
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        $this->ensureCompanyExists($context);
        if ($this->argsModifier) {
            $args = $this->argsModifier->modifyArgs($field, $context, $info, $args);
        }

        $searchCriteria = $this->searchCriteriaBuilder->build($field->getName(), $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $searchResult = $this->dataProvider->getListData($searchCriteria);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getItems(),
            'page_info' => [
                'page_size' => $searchCriteria->getPageSize(),
                'current_page' => $this->resolveCurrentPage($searchCriteria, $searchResult)
            ]
        ];
    }
    /**
     * Validate arguments
     *
     * @param array $args
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('`currentPage` value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('`pageSize` value must be greater than 0.'));
        }
    }

    /**
     * Resolve current page
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchResultsInterface $searchResult
     * @return int
     */
    private function resolveCurrentPage(
        SearchCriteriaInterface $searchCriteria,
        SearchResultsInterface $searchResult
    ): int {
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

    /**
     * Ensure company exists
     *
     * @param ContextInterface $context
     * @return void
     * @throws GraphQlAuthorizationException
     */
    private function ensureCompanyExists(ContextInterface $context): void
    {
        if ($this->isCompanyRequired) {
            if (false === $context->getExtensionAttributes()->getIsCustomer()) {
                throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
            }

            $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($context->getUserId());
            if (!$companyUser) {
                throw new GraphQlAuthorizationException(__('Logged in customer doesn\'t belong to any company'));
            }
        }
    }
}
