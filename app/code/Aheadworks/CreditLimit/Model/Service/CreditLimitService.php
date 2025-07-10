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
namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionParametersFactory;
use Aheadworks\CreditLimit\Api\TransactionManagementInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\CartChecker;
use Aheadworks\CreditLimit\Model\Service\TransactionParametersService;

/**
 * Class CreditLimitService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CreditLimitService implements CreditLimitManagementInterface
{
    /**
     * @var TransactionManagementInterface
     */
    private $transactionService;

    /**
     * @var TransactionParametersFactory
     */
    private $transactionParametersFactory;

    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var CartChecker
     */
    private $cartChecker;

    /**
     * @var TransactionParametersService
     */
    private $transactionParametersService;

    /**
     * CreditLimitService constructor.
     *
     * @param TransactionManagementInterface $transactionService
     * @param TransactionParametersFactory $transactionParametersFactory
     * @param SummaryRepositoryInterface $summaryRepository
     * @param CartChecker $cartChecker
     * @param TransactionParametersService $transactionParametersService
     */
    public function __construct(
        TransactionManagementInterface $transactionService,
        TransactionParametersFactory $transactionParametersFactory,
        SummaryRepositoryInterface $summaryRepository,
        CartChecker $cartChecker,
        TransactionParametersService $transactionParametersService
    ) {
        $this->transactionService = $transactionService;
        $this->transactionParametersFactory = $transactionParametersFactory;
        $this->summaryRepository = $summaryRepository;
        $this->cartChecker = $cartChecker;
        $this->transactionParametersService = $transactionParametersService;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_LIMIT_CHANGED,
                TransactionParametersInterface::CREDIT_LIMIT => $creditLimit,
                TransactionParametersInterface::IS_CUSTOM_CREDIT_LIMIT => true,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * Get transaction params by credit limit data for specified customer.
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @param SummaryInterface|null $summary
     * @param int|null $customerGroupId
     * @return TransactionParametersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTransactionParams(
        int $customerId,
        float $creditLimit,
        string $commentToAdmin = '',
        string $commentToCustomer = '',
        $summary = null,
        $customerGroupId = null,
    ): TransactionParametersInterface {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_LIMIT_CHANGED,
                TransactionParametersInterface::CREDIT_LIMIT => $creditLimit,
                TransactionParametersInterface::IS_CUSTOM_CREDIT_LIMIT => false,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer
            ]
        );
        if ($summary) {
            $this->transactionParametersService->setSummaryExtensionData($transactionParams, $summary);
        }
        if (!is_null($customerGroupId)) {
            $this->transactionParametersService->setCustomerGroupIdExtensionData(
                $transactionParams,
                (int)$customerGroupId
            );
        }

        return $transactionParams;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateDefaultCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_LIMIT_CHANGED,
                TransactionParametersInterface::CREDIT_LIMIT => $creditLimit,
                TransactionParametersInterface::IS_CUSTOM_CREDIT_LIMIT => false,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateCreditBalance(
        $customerId,
        $amount,
        $currency = null,
        $commentToAdmin = '',
        $commentToCustomer = '',
        $poNumber = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_BALANCE_UPDATED,
                TransactionParametersInterface::AMOUNT => $amount,
                TransactionParametersInterface::AMOUNT_CURRENCY => $currency,
                TransactionParametersInterface::USED_CURRENCY => $currency,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer,
                TransactionParametersInterface::PO_NUMBER => $poNumber,
                TransactionParametersInterface::IS_ALLOWED_TO_EXCEED_LIMIT => true
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function spendCreditBalanceOnOrder($customerId, $order)
    {
        $summary = $this->summaryRepository->getByCustomerId($customerId);
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::ORDER_PURCHASED,
                TransactionParametersInterface::AMOUNT => -$order->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $order->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $order->getOrderCurrencyCode(),
                TransactionParametersInterface::PO_NUMBER => $order->getPayment()->getPoNumber(),
                TransactionParametersInterface::ORDER_ENTITY => $order,
                TransactionParametersInterface::IS_ALLOWED_TO_EXCEED_LIMIT => $summary->getIsAllowedToExceed()
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function decreaseCreditBalanceByUnitRefund($customerId, $creditmemo)
    {
        $creditmemoItems = $creditmemo->getAllItems();
        /** @var InvoiceItem $invoiceItem */
        foreach ($creditmemoItems as $creditmemoItem) {
            if ($this->cartChecker->isItemBalanceUnit($creditmemoItem)) {
                $transactionParams = $this->transactionParametersFactory->create(
                    [
                        TransactionParametersInterface::CUSTOMER_ID => $customerId,
                        TransactionParametersInterface::ACTION => Action::CREDIT_BALANCE_REFUNDED_BY_UNIT,
                        TransactionParametersInterface::AMOUNT => -$creditmemoItem->getBaseRowTotal(),
                        TransactionParametersInterface::AMOUNT_CURRENCY => $creditmemo->getBaseCurrencyCode(),
                        TransactionParametersInterface::USED_CURRENCY => $creditmemo->getOrderCurrencyCode(),
                        TransactionParametersInterface::ORDER_ENTITY => $creditmemo->getOrder(),
                        TransactionParametersInterface::CREDITMEMO_ENTITY => $creditmemo,
                        TransactionParametersInterface::IS_ALLOWED_TO_EXCEED_LIMIT => true
                    ]
                );
                $this->transactionService->createTransaction($transactionParams);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function increaseCreditBalanceByUnitPurchase($customerId, $invoice)
    {
        if (!$invoice->wasPayCalled()) {
            return false;
        }

        $invoiceItems = $invoice->getAllItems();
        /** @var InvoiceItem $invoiceItem */
        foreach ($invoiceItems as $invoiceItem) {
            if ($this->cartChecker->isItemBalanceUnit($invoiceItem)) {
                $transactionParams = $this->transactionParametersFactory->create(
                    [
                        TransactionParametersInterface::CUSTOMER_ID => $customerId,
                        TransactionParametersInterface::ACTION => Action::CREDIT_BALANCE_UPDATED_BY_UNIT,
                        TransactionParametersInterface::AMOUNT => $invoiceItem->getBaseRowTotal(),
                        TransactionParametersInterface::AMOUNT_CURRENCY => $invoice->getBaseCurrencyCode(),
                        TransactionParametersInterface::USED_CURRENCY => $invoice->getOrderCurrencyCode(),
                        TransactionParametersInterface::ORDER_ENTITY => $invoice->getOrder(),
                        TransactionParametersInterface::IS_ALLOWED_TO_EXCEED_LIMIT => true
                    ]
                );
                $this->transactionService->createTransaction($transactionParams);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function reimburseCreditBalanceOnCanceledOrder($customerId, $order)
    {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::ORDER_CANCELED,
                TransactionParametersInterface::AMOUNT => $order->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $order->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $order->getOrderCurrencyCode(),
                TransactionParametersInterface::ORDER_ENTITY => $order
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function refundCreditBalanceOnCreditmemo($customerId, $order, $creditmemo)
    {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_MEMO_REFUNDED,
                TransactionParametersInterface::AMOUNT => $creditmemo->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $creditmemo->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $creditmemo->getOrderCurrencyCode(),
                TransactionParametersInterface::ORDER_ENTITY => $order,
                TransactionParametersInterface::CREDITMEMO_ENTITY => $creditmemo
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }
}
