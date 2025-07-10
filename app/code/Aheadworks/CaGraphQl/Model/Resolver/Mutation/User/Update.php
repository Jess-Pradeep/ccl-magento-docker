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
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Import\User\Validator\RoleValidator;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;

class Update extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param RoleValidator $roleValidator
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly DataObjectHelper $dataObjectHelper,
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
        $user = $this->customerRepository->getById($args[CustomerInterface::ID]);

        $result = $this->roleValidator->validate($args['extension_attributes']['aw_ca_company_user'], 1);
        if ($result->getErrors()) {
            $errors = $result->getErrors();
            throw new GraphQlAuthorizationException(reset($errors));
        }

        $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
        if (!$companyUser || $companyUser->getCompanyId()
            != $this->companyUserProvider->getCurrentCompanyUser()->getCompanyId()
        ) {
            throw new GraphQlAuthorizationException(__('User doesn\'t belong to company'));
        }

        $userData = $this->dataObjectProcessor->buildOutputDataArray($user, CustomerInterface::class);
        $this->dataObjectHelper->populateWithArray(
            $user,
            array_replace_recursive($userData, $args),
            CustomerInterface::class
        );

        $this->companyUserManagement->saveUser($user);
        return $this->dataObjectProcessor->buildOutputDataArray($user, CustomerInterface::class);
    }
}
