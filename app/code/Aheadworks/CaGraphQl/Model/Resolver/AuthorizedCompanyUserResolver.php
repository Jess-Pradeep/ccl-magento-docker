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
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

abstract class AuthorizedCompanyUserResolver
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     */
    public function __construct(
        protected readonly CompanyUserProvider $companyUserProvider,
    ) {
    }

    /**
     * Ensure company user
     *
     * @param ContextInterface $context
     * @return void
     * @throws GraphQlAuthorizationException
     */
    protected function ensureCompanyUserAuthorized(ContextInterface $context): void
    {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($context->getUserId());
        if (!$companyUser) {
            throw new GraphQlAuthorizationException(__('The current user doesn\'t belong to company'));
        }
    }
}
