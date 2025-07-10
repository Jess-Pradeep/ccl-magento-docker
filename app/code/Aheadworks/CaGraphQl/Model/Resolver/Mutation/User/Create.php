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

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\User;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Aheadworks\Ca\Model\Import\User\Validator\RoleValidator;
use Magento\Framework\Reflection\DataObjectProcessor;

class Create extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param RequestInterfaceFactory $requestFactory
     * @param DataProcessor $dataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param CompanyRepositoryInterface $companyRepository
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param RoleValidator $roleValidator
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly RequestInterfaceFactory $requestFactory,
        private readonly DataProcessor $dataProcessor,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly AuthorizationManagementInterface $authorizationManagement,
        private readonly RoleValidator $roleValidator,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly CompanyUserManagementInterface $companyUserManagement
    ) {
        parent::__construct($companyUserProvider);
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
        $this->ensureCompanyUserAuthorized($context);
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $request->setParams($args);
        $companyUser = $this->dataProcessor->prepareCustomer($request);

        $result = $this->roleValidator->validate($args['extension_attributes']['aw_ca_company_user'], 1);
        if ($result->getErrors()) {
            $errors = $result->getErrors();
            throw new GraphQlAuthorizationException(reset($errors));
        }

        $this->dataObjectHelper->populateWithArray(
            $companyUser,
            $args,
            CustomerInterface::class
        );

        $currentCompanyUser = $this->companyUserProvider->getCurrentCompanyUser();
        $company = $this->companyRepository->get($currentCompanyUser->getCompanyId());
        $companyUser->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyGroupId($currentCompanyUser->getCompanyGroupId())
            ->setCompanyId($company->getId());
        $companyUser->setGroupId($company->getCustomerGroupId());
        $this->companyUserManagement->saveUser($companyUser);
        return $this->dataObjectProcessor->buildOutputDataArray($companyUser, CustomerInterface::class);
    }
}
