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
namespace Aheadworks\CreditLimit\Model\Customer\Backend;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;

/**
 * Class BalanceUpdater
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Backend
 */
class BalanceUpdater
{
    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param CustomerManagementInterface $customerManagement
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(
        CreditLimitManagementInterface $creditLimitManagement,
        CustomerManagementInterface $customerManagement,
        SummaryRepositoryInterface $summaryRepository
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
        $this->customerManagement = $customerManagement;
        $this->summaryRepository = $summaryRepository;
    }

    /**
     * Update credit limit
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @param array $defaultData
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditLimit($customerId, $creditLimitData, $defaultData)
    {
        $useDefaultCreditLimit = $defaultData[ParamsInterface::CREDIT_LIMIT] ?? false;

        $creditLimit = $creditLimitData[ParamsInterface::CREDIT_LIMIT] ?? null;
        if (empty($creditLimit) && !strlen((string)$creditLimit)) {
            return false;
        }

        $adminComment = $creditLimitData['credit_limit_' . ParamsInterface::COMMENT_TO_ADMIN] ?? null;
        if (!(bool)$useDefaultCreditLimit && $this->isCreditLimitChanged($customerId, $creditLimit)) {
            return $this->creditLimitManagement->updateCreditLimit(
                $customerId,
                $creditLimit,
                $adminComment
            );
        }
        if ((bool)$useDefaultCreditLimit && $this->customerManagement->isCreditLimitCustom($customerId)) {
            return $this->creditLimitManagement->updateDefaultCreditLimit(
                $customerId,
                0,
                $adminComment
            );
        }

        return false;
    }

    /**
     * Update credit balance
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditBalance($customerId, $creditLimitData)
    {
        $amount = $creditLimitData[ParamsInterface::AMOUNT] ?? null;
        if (empty($amount) && !strlen((string)$amount)) {
            return false;
        }
        if (!$this->customerManagement->isCreditLimitAvailable($customerId)) {
            throw new LocalizedException(__('Please specify Credit Limit for customer before updating amount'));
        }

        $adminComment = $creditLimitData['balance_' . ParamsInterface::COMMENT_TO_ADMIN] ?? null;
        $customerComment = $creditLimitData['balance_' . ParamsInterface::COMMENT_TO_CUSTOMER] ?? null;
        $poNumber = $creditLimitData[ParamsInterface::PO_NUMBER] ?? null;
        $currency = $creditLimitData[ParamsInterface::AMOUNT . '_currency'] ?? null;
        return $this->creditLimitManagement->updateCreditBalance(
            $customerId,
            $amount,
            $currency,
            $adminComment,
            $customerComment,
            $poNumber
        );
    }

    /**
     * Update is allowed to exceed flag
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @return bool
     * @throws LocalizedException
     */
    public function updateIsAllowedToExceedFlag($customerId, $creditLimitData)
    {
        $isAllowed = $creditLimitData[SummaryInterface::IS_ALLOWED_TO_EXCEED] ?? null;
        if (isset($isAllowed) && $this->customerManagement->isCreditLimitAvailable($customerId)) {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            if ($summary->getIsAllowedToExceed() != $isAllowed) {
                $summary->setIsAllowedToExceed($isAllowed);
                $this->summaryRepository->save($summary);
                return true;
            }
        }

        return false;
    }

    /**
     * Is credit limit changed
     *
     * @param int $customerId
     * @param float $newCreditLimitAmount
     * @return bool
     */
    private function isCreditLimitChanged($customerId, $newCreditLimitAmount)
    {
        $originalCreditLimit = $this->customerManagement->getCreditLimitAmount($customerId);

        return $newCreditLimitAmount != $originalCreditLimit;
    }
}
