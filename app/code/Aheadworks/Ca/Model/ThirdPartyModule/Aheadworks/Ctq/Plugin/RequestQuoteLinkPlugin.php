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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class RequestQuoteLinkPlugin
 */
class RequestQuoteLinkPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyManagement;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CheckoutSession $checkoutSession
     * @param SellerCompanyManagementInterface $sellerCompanyManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        SellerCompanyManagementInterface $sellerCompanyManagement,
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->sellerCompanyManagement = $sellerCompanyManagement;
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Check if need request button for company customer
     *
     * @param ArgumentInterface $subject
     * @param bool $result
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterIsRequestQuoteButtonAvailable(ArgumentInterface $subject, bool $result): bool
    {
        $company = $this->sellerCompanyManagement->getCompanyByCustomerId($this->checkoutSession->getQuote()->getCustomerId());
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            return $result && $company->getIsAllowedToQuote()
                && $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_allow_using');
        }

        return $result;
    }
}
