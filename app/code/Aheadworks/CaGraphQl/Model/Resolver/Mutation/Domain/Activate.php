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

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\Domain;

use Aheadworks\Ca\Api\CompanyDomainRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Source\Company\Domain\AdminType;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Aheadworks\Ca\Model\Source\Company\Domain\Status as DomainStatus;

class Activate extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param CommandInterface $saveDomainCommand
     * @param CompanyDomainRepositoryInterface $domainRepository
     * @param string $receivedStatus
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly CommandInterface $saveDomainCommand,
        private readonly CompanyDomainRepositoryInterface $domainRepository,
        private readonly string $receivedStatus = DomainStatus::ACTIVE
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
     * @return CompanyDomainInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): CompanyDomainInterface {
        $this->ensureCompanyUserAuthorized($context);
        $domain = $this->domainRepository->get($args[CompanyDomainInterface::ID]);
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($context->getUserId());

        if ($domain->getCompanyId() != $companyUser->getCompanyId()) {
            throw new GraphQlAuthorizationException(__('This domain doesn\'t belong to current company'));
        }

        $args[CompanyDomainInterface::REQUESTED_BY] = AdminType::COMPANY_ADMIN;
        $args[CompanyDomainInterface::STATUS] = $this->receivedStatus;

        return $this->saveDomainCommand->execute($args);
    }
}
