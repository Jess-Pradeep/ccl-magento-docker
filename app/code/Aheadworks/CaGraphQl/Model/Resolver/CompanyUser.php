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
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;

class CompanyUser extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($companyUserProvider);
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
        $this->ensureCompanyUserAuthorized($context);
        $customer = $this->customerRepository->getById($args['customer_id']);

        $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        if (!$companyUser || $companyUser->getCompanyId()
            != $this->companyUserProvider->getCurrentCompanyUser()->getCompanyId()
        ) {
            throw new GraphQlAuthorizationException(__('User doesn\'t belong to company'));
        }

        return $this->dataObjectProcessor->buildOutputDataArray(
            $customer,
            CustomerInterface::class
        );
    }
}
