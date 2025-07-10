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

namespace Aheadworks\Ca\Model\Import\Company\Command;

use Aheadworks\Ca\Model\Export\ColumnProviderInterface;
use Aheadworks\Ca\Model\Import\Command\CommandInterface;
use Aheadworks\Ca\Model\Import\Company\Converter\Company as CompanyConverter;
use Aheadworks\Ca\Model\Import\Company\Converter\Customer as CustomerConverter;
use Aheadworks\Ca\Model\Import\Converter\DataType as DataTypeConverter;
use Aheadworks\Ca\Model\Import\ImportEntity;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Data\CommandInterface as DataCommandInterface;
use Aheadworks\Ca\Model\Data\Command\Company\ProcessSaving as ProcessSaving;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company\Updater
    as CreditLimitUpdater;

/**
 * Append command
 */
class Append implements CommandInterface
{
    /**
     * @param CompanyConverter $companyConverter
     * @param CustomerConverter $customerConverter
     * @param DataCommandInterface $processSavingCommand
     * @param ColumnProviderInterface $columnProvider
     * @param DataTypeConverter $dataTypeConverter
     * @param CreditLimitUpdater $creditLimitUpdater
     */
    public function __construct(
        private readonly CompanyConverter $companyConverter,
        private readonly CustomerConverter $customerConverter,
        private readonly DataCommandInterface $processSavingCommand,
        private readonly ColumnProviderInterface $columnProvider,
        private readonly DataTypeConverter $dataTypeConverter,
        private readonly CreditLimitUpdater $creditLimitUpdater
    ) {
    }

    /**
     * Executes the current command
     *
     * @param array $bunch
     * @param ImportEntity $importEntity
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(array $bunch, ImportEntity $importEntity): void
    {
        foreach ($bunch as $dataRow) {
            $dataRow = $this->prepareDataRow($dataRow);
            $company = $this->companyConverter->convert($dataRow);
            $customer = $this->customerConverter->convert($dataRow);

            try {
                $result = $this->processSavingCommand->execute([
                    'customer' => $customer,
                    'company' => $company
                ]);

                if (isset($dataRow['credit_limit'])) {
                    $this->creditLimitUpdater->updateCreditLimit(
                        $company->getId(),
                        [
                            'credit_limit' => $dataRow['credit_limit']
                        ]
                    );
                }

                if ($result[ProcessSaving::RESULT_CREATED]) {
                    $importEntity->incrementCreatedItemsCount();
                }
                if ($result[ProcessSaving::RESULT_UPDATED]) {
                    $importEntity->incrementUpdatedItemsCount();
                }
            } catch (\Exception $exception) {
                throw new LocalizedException(__(
                    'An error has occurred in company with email "%1", company admin email "%2", error message: "%3"',
                    $company->getEmail(),
                    $customer->getEmail(),
                    $exception->getMessage()
                ));
            }
        }
    }

    /**
     * Prepare data row
     *
     * @param array $dataRow
     * @return array
     */
    public function prepareDataRow(array $dataRow): array
    {
        $columns = $this->columnProvider->getColumns();
        return $this->dataTypeConverter->prepareDataRow($columns, $dataRow);
    }
}
