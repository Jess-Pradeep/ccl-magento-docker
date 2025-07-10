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

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\GraphQl\Model\Query\ContextInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;

class CompanyForCurrentCustomer implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param CompanyRepositoryInterface $companyRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        private readonly CompanyUserProvider $companyUserProvider,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly DataObjectProcessor $dataObjectProcessor,
    ) {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws Exception
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): ?array {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($context->getUserId());
        if ($companyUser) {
            $company = $this->companyRepository->get($companyUser->getCompanyId());
            return $this->dataObjectProcessor->buildOutputDataArray(
                $company,
                CompanyInterface::class
            );
        }

        return null;
    }
}
