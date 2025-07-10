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

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\Role;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Data\Command\Role\Save as RoleSaveCommand;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Save extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param RoleInterfaceFactory $roleFactory
     * @param RoleRepositoryInterface $roleRepository
     * @param CommandInterface $saveRoleCommand
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly RoleInterfaceFactory $roleFactory,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly CommandInterface $saveRoleCommand,
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
     * @return RoleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): RoleInterface {
        if (isset($args[RoleInterface::ID])) {
            $role = $this->roleRepository->get($args[RoleInterface::ID]);
            if ($role->getCompanyId() != $this->companyUserProvider->getCurrentCompanyUser()->getCompanyId()) {
                throw new GraphQlAuthorizationException(__('Role doesn\'t belong to company'));
            }
        }

        $args[RoleSaveCommand::CURRENT_COMPANY_ID]
            = $this->companyUserProvider->getCurrentCompanyUser()->getCompanyId();
        $role = $this->saveRoleCommand->execute($args);
        return $this->roleRepository->get($role->getId(), true);
    }
}
