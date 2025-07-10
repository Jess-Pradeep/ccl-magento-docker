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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Plugin\Model\Cart;

use Magento\Quote\Model\Cart\CustomerCartResolver;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;

/**
 * Class CustomerCartResolverPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\Cart
 */
class CustomerCartResolverPlugin
{
    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        AuthorizationManagementInterface $authorizationManagement
    ) {
        $this->companyUserProvider = $companyUserProvider;
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * Check current logged in customer and adjust customer ID
     *
     * @param CustomerCartResolver $subject
     * @param int $customerId
     * @param string|null $predefinedMaskedQuoteId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeResolve($subject, int $customerId, ?string $predefinedMaskedQuoteId = null): array
    {
        $companyUser = $this->companyUserProvider->getCurrentCompanyUser();
        if ($companyUser
            && $this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_sales')
        ) {
            $customerId = $companyUser->getCustomerId();
        }

        return [$customerId, $predefinedMaskedQuoteId];
    }
}
