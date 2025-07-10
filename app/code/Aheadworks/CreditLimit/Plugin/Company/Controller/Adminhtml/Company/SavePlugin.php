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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Plugin\Company\Controller\Adminhtml\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\ThirdPartyModule\Aheadworks\Ca\Model\Service\CompanyManagementService;
use Magento\Framework\App\ActionInterface;

/**
 * Class SavePlugin
 */
class SavePlugin
{
    /**
     * SavePlugin constructor.
     *
     * @param RequestInterface $request
     * @param PaymentPeriodManagementInterface $paymentPeriodService
     * @param CompanyManagementService $companyManagement
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        private RequestInterface $request,
        private PaymentPeriodManagementInterface $paymentPeriodService,
        private CompanyManagementService $companyManagement,
        private CustomerManagementInterface $customerManagement
    ) {
    }

    /**
     * Update payment period data after company save
     *
     * @param ActionInterface $subject
     * @param mixed $result
     * @param CompanyInterface $company
     * @return mixed
     * @throws LocalizedException
     */
    public function afterPostCompanySave(ActionInterface $subject, $result,  $company)
    {
        $creditLimitData = $this->request->getParam('aw_credit_limit');
        if (isset($creditLimitData[SummaryInterface::PAYMENT_PERIOD])) {
            $rootUser = $this->companyManagement->getRootUserByCompanyId($company->getId());
            $paymentPeriod = $creditLimitData[SummaryInterface::PAYMENT_PERIOD];
            if ($paymentPeriod === '' || $paymentPeriod === null) {
                $paymentPeriod = null;
            } else {
                $paymentPeriod = (int)$paymentPeriod;
            }
            if ($rootUser) {
                $isCreditLimitAvailable = $this->customerManagement->isCreditLimitAvailable($rootUser->getId());
                if ($paymentPeriod !== null && !$isCreditLimitAvailable) {
                    throw new LocalizedException(
                        __('Please specify Credit Limit for customer before updating payment period')
                    );
                }
                $customerId = (int)$rootUser->getId();
                if ($isCreditLimitAvailable) {
                    if (isset($creditLimitData[SummaryInterface::DUE_DATE]) &&
                        strlen($creditLimitData[SummaryInterface::DUE_DATE]) === 0) {
                        $this->paymentPeriodService->resetDueDate($customerId, true);
                    }
                    if (!$this->paymentPeriodService->isSamePaymentPeriod($paymentPeriod, $customerId)) {
                        $this->paymentPeriodService->updatePeriod(
                            $paymentPeriod,
                            $customerId
                        );
                    }
                }
            }
        }
        return $result;
    }
}
