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

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status as CompanyUserStatus;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Activate extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param CommandInterface $changeStatusCommand
     * @param CustomerRepositoryInterface $customerRepository
     * @param int $receivedStatus
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly CommandInterface $changeStatusCommand,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly int $receivedStatus = CompanyUserStatus::ACTIVE
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
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): bool {
        $this->ensureCompanyUserAuthorized($context);
        $user = $this->customerRepository->getById($args['customer_id']);

        $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
        if (!$companyUser || $companyUser->getCompanyId()
            != $this->companyUserProvider->getCurrentCompanyUser()->getCompanyId()
        ) {
            throw new GraphQlAuthorizationException(__('User doesn\'t belong to company'));
        }

        $data = [
            'customer_id' => $user->getId(),
            'status' => $this->receivedStatus
        ];
        return $this->changeStatusCommand->execute($data);
    }
}
