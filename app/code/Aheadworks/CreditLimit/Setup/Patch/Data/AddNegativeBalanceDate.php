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

namespace Aheadworks\CreditLimit\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary;

class AddNegativeBalanceDate implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply(): void
    {
        $this->addNegativeBalanceDate($this->moduleDataSetup);
    }

    /**
     * Get aliases
     *
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Fill in the column negative balance date
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function addNegativeBalanceDate(ModuleDataSetupInterface $setup): void
    {
        $columns = [
            SummaryInterface::SUMMARY_ID =>
                Transaction::MAIN_TABLE_NAME . '.' . TransactionInterface::SUMMARY_ID,
            SummaryInterface::NEGATIVE_BALANCE_DATE =>
                'MAX(' . Transaction::MAIN_TABLE_NAME . '.' . TransactionInterface::CREATED_AT . ')'
        ];
        $clTransactionSelect = $setup->getConnection()->select()
            ->from([Transaction::MAIN_TABLE_NAME => $setup->getTable(Transaction::MAIN_TABLE_NAME)])
            ->group(Transaction::MAIN_TABLE_NAME . '.' . TransactionInterface::SUMMARY_ID)
            ->reset('columns')
            ->columns($columns);

        $positiveClTransactionSelect = clone $clTransactionSelect;
        $positiveClTransactionSelect->where(TransactionInterface::CREDIT_BALANCE . ' >= 0');
        $positiveTransactions = $setup->getConnection()->fetchPairs($positiveClTransactionSelect);

        $negativeClTransactionSelect = clone $clTransactionSelect;
        $negativeClTransactionSelect->where(TransactionInterface::CREDIT_BALANCE . ' < 0');
        $negativeTransactions = $setup->getConnection()->fetchAll($negativeClTransactionSelect);

        foreach ($negativeTransactions as $negativeTransaction) {
            $positiveTransactionDate = $positiveTransactions[$negativeTransaction[SummaryInterface::SUMMARY_ID]] ?? null;
            if (!$positiveTransactionDate
                || $negativeTransaction[SummaryInterface::NEGATIVE_BALANCE_DATE] > $positiveTransactionDate) {
                $setup->getConnection()->insertOnDuplicate(
                    $setup->getTable(CreditSummary::MAIN_TABLE_NAME),
                    $negativeTransaction
                );
            }
        }
    }
}
