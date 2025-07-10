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

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\CreditSummary\Checker as CreditSummaryChecker;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class PaymentPeriod
 */
class PaymentPeriod extends AbstractBuilder
{
    /**
     * PaymentPeriod constructor.
     *
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     * @param PaymentPeriodManagementInterface $paymentPeriodService
     * @param CreditSummaryChecker $creditSummaryChecker
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement,
        private PaymentPeriodManagementInterface $paymentPeriodService,
        private CreditSummaryChecker $creditSummaryChecker,
    ) {
        parent::__construct($transactionActionSource, $summaryManagement);
    }

    /**
     * Check if provided parameters are valid for current builder
     *
     * @param TransactionParametersInterface $params
     * @return bool
     * @throws LocalizedException
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId(), true);

        if (!$this->isPlaceOrderAvailableByPaymentPeriod($params, $summary)) {
            throw new ValidatorException(__(
                'Please update the credit balance to place the order'
            ));
        }

        return true;
    }

    /**
     * Fill up transaction object with data
     *
     * @param TransactionInterface $transaction
     * @param TransactionParametersInterface $params
     * @throws LocalizedException
     * @return void
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params): void
    {
        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId());
        if ($transaction->getAction() === TransactionActionSource::CREDIT_BALANCE_REFUNDED_BY_UNIT) {
            $this->restorePaymentPeriodDueDate($summary);
        } elseif ($transaction->getAction() === TransactionActionSource::CREDIT_BALANCE_UPDATED_BY_UNIT) {
            $this->resetPaymentPeriodDueDate($summary);
        }
    }

    /**
     * Is place order available by payment period
     *
     * @param TransactionParametersInterface $params
     * @param SummaryInterface $summary
     * @return bool
     * @throws LocalizedException
     */
    private function isPlaceOrderAvailableByPaymentPeriod(
        TransactionParametersInterface $params,
        SummaryInterface $summary
    ): bool {
        if ($params->getAction() !== TransactionActionSource::ORDER_PURCHASED) {
            return true;
        }

        return $this->paymentPeriodService->isPlaceOrderAvailable((int)$summary->getCustomerId());
    }

    /**
     * Restore payment period due date
     *
     * @param SummaryInterface $summary
     * @throws LocalizedException
     */
    private function restorePaymentPeriodDueDate(SummaryInterface $summary): void
    {
        $customerId = (int)$summary->getCustomerId();
        $this->paymentPeriodService->restoreDueDate($customerId);
    }

    /**
     * Reset payment period due date
     *
     * @param SummaryInterface $summary
     * @return void
     * @throws LocalizedException
     */
    private function resetPaymentPeriodDueDate(SummaryInterface $summary): void
    {
        $customerId = (int)$summary->getCustomerId();
        $dueDate = $this->paymentPeriodService->getDueDate($customerId);
        if ($dueDate && $this->creditSummaryChecker->isCreditBalancePositive($summary)) {
            $this->paymentPeriodService->resetDueDate($customerId);
        }
    }
}
