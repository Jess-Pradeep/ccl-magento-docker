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
 * @package    CreditLimitGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimitGraphQl\Model\Resolver\Customer;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionAction;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Formatter;

/**
 * Class CreditHistory customer info
 */
class CreditHistory implements ResolverInterface
{
    /**
     * CreditHistory constructor.
     *
     * @param CustomerManagementInterface $customerService
     * @param TransactionCollectionFactory $collectionFactory
     * @param SummaryRepositoryInterface $summaryRepository
     * @param TransactionAction $transactionAction
     * @param Formatter $formatter
     */
    public function __construct(
        private CustomerManagementInterface $customerService,
        private TransactionCollectionFactory $collectionFactory,
        private SummaryRepositoryInterface $summaryRepository,
        private TransactionAction $transactionAction,
        private Formatter $formatter
    ) {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $customerId = (int)$context->getUserId();
        $output = [];

        if ($this->customerService->isCreditLimitAvailable($customerId)) {
            $historyItems = $this->getCreditHistory($customerId);

            foreach ($historyItems as $historyItem) {
                $transactionData = $historyItem->getData();
                $amount = $this->getTransactionAmount($transactionData);
                $creditBalance = $this->formatter->getFormattedBalanceData(
                    (float)$transactionData[TransactionInterface::CREDIT_BALANCE],
                    $transactionData[TransactionInterface::CREDIT_CURRENCY]
                );
                $creditAvailable = $this->formatter->getFormattedBalanceData(
                    (float)$transactionData[TransactionInterface::CREDIT_AVAILABLE],
                    $transactionData[TransactionInterface::CREDIT_CURRENCY]
                );
                $creditLimit = $this->formatter->getFormattedBalanceData(
                    (float)$transactionData[TransactionInterface::CREDIT_LIMIT],
                    $transactionData[TransactionInterface::CREDIT_CURRENCY]
                );
                $output[] = [
                    'date' => $transactionData[TransactionInterface::CREATED_AT],
                    'action' => (string)$this->transactionAction->getActionLabel(
                        $transactionData[TransactionInterface::ACTION]
                    ),
                    'amount' => $amount,
                    'credit_balance' => $creditBalance,
                    'available_credit' => $creditAvailable,
                    'credit_limit' => $creditLimit,
                    'purchase_order' => $transactionData[TransactionInterface::PO_NUMBER],
                    'updated_by' => $transactionData[TransactionInterface::UPDATED_BY],
                    'comment_to_admin' => $transactionData[TransactionInterface::COMMENT_TO_ADMIN],
                    'comment_to_customer' => $transactionData[TransactionInterface::COMMENT_TO_CUSTOMER],
                ];
            }
        }

        return $output;
    }

    /**
     * Get transaction amount
     *
     * @param array $transactionData
     * @return array|null
     */
    private function getTransactionAmount(array $transactionData): ?array
    {
        $result = null;
        if (in_array(
            $transactionData[TransactionInterface::ACTION],
            $this->transactionAction->getActionsToUpdateCreditBalance()
        )) {
            $result = $this->formatter->getFormattedAmountData($transactionData, true);
        }

        return $result;
    }

    /**
     * Get credit history
     *
     * @param int $customerId
     * @return DataObject[]
     * @throws NoSuchEntityException
     */
    private function getCreditHistory(int $customerId): array
    {
        $summary = $this->summaryRepository->getByCustomerId($customerId);
        $transactionCollection = $this->collectionFactory->create();
        $companyId = $summary->getCompanyId();
        if ($companyId) {
            $transactionCollection->addFieldToFilter('main_table.company_id', $companyId);
        } else {
            $transactionCollection->addFieldToFilter('customer_id', $customerId);
        }
        $transactionCollection->setOrder('created_at', 'DESC');

        return $transactionCollection->getItems();
    }
}
