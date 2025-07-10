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

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Api\DataObjectHelper;

class Update implements ResolverInterface
{
    /**
     * @param CompanyRepositoryInterface $companyRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param SellerCompanyManagementInterface $sellerCompanyService
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly SellerCompanyManagementInterface $sellerCompanyService,
        private readonly DataObjectHelper $dataObjectHelper
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
     * @return CompanyInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): CompanyInterface {
        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $company = $this->companyRepository->get($args['company'][CompanyInterface::ID]);
        $customer = $this->customerRepository->get(
            $args[CustomerInterface::EMAIL],
            $context->getExtensionAttributes()->getStore()->getWebsiteId()
        );

        $this->dataObjectHelper->populateWithArray(
            $company,
            $args['company'],
            CompanyInterface::class
        );

        $this->dataObjectHelper->populateWithArray(
            $customer,
            $args,
            CustomerInterface::class
        );

        return $this->sellerCompanyService->updateCompany($company, $customer);
    }
}
