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

namespace Aheadworks\Ca\Model\Import\User;

use Aheadworks\Ca\Model\Import\ValidatorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\ImportExport\Model\Import\AbstractEntity as ImportAbstractEntity;
use Magento\ImportExport\Model\Import\AbstractSource;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Aheadworks\Ca\Model\Import\User\Validator\IntegrityValidator;

/**
 * User validator
 */
class Validator
{
    /**
     * @var int
     */
    private int $processedRecords = 0;

    /**
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param ValidatorInterface $validator
     * @param int $errorsCountToStop
     */
    public function __construct(
        private readonly ProcessingErrorAggregatorInterface $errorAggregator,
        private readonly ValidatorInterface $validator,
        private readonly int $errorsCountToStop = 10
    ) {
    }

    /**
     * Validates source file and returns validation result
     *
     * Before validate data the method requires to initialize error aggregator (ProcessingErrorAggregatorInterface)
     * with 'validation strategy' and 'allowed error count' values to allow using this parameters in validation process.
     *
     * @param AbstractSource $source
     * @return array
     * @throws LocalizedException
     */
    public function validateSource(AbstractSource $source): array
    {
        $this->errorAggregator->clear();
        $this->errorAggregator->initValidationStrategy(
            'validation-stop-on-errors',
            $this->errorsCountToStop
        );

        try {
            $this->validateData($source);
        } catch (\Exception $e) {
            $this->errorAggregator->addError(
                AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION,
                ProcessingError::ERROR_LEVEL_CRITICAL,
                null,
                null,
                $e->getMessage()
            );
        }

        return $this->getOperationResultMessages($this->errorAggregator);
    }

    /**
     * Validate data
     *
     * @param AbstractSource $source
     * @return void
     */
    private function validateData(AbstractSource $source): void
    {
        $this->errorAggregator->clear();
        $absentColumns = array_diff(IntegrityValidator::REQUIRED_FIELDS, $source->getColNames());
        $this->addMissingColumnsError($absentColumns);

        if (!$this->errorAggregator->getErrorsCount()) {
            $source->rewind();
            while ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->processedRecords);
                    $this->processedRecords++;
                    $source->next();
                    continue;
                }

                $this->validateRow($rowData, $source->key());
                $this->processedRecords++;
                if ($this->errorAggregator->isErrorLimitExceeded()) {
                    break;
                }
                $source->next();
            }
        }
    }

    /**
     * Return operation result messages
     *
     * @param ProcessingErrorAggregatorInterface $validationResult
     * @return string[]
     */
    private function getOperationResultMessages(ProcessingErrorAggregatorInterface $validationResult): array
    {
        $messages = [];
        if (!$validationResult->getAllErrors() && !$this->processedRecords) {
            $messages[] = __('This file does not contain any data.');
        }

        foreach ($validationResult->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
            $error = $errorMessage . ' ' . __('in row(s)') . ': ' . implode(', ', $rows);
            $messages[] = $error;
        }

        return $messages;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNum
     * @return void
     */
    private function validateRow(array $rowData, int $rowNum): void
    {
        $result = $this->validator->validate($rowData, $rowNum);
        if ($result->isValid()) {
            return;
        }

        foreach ($result->getErrors() as $error) {
            $this->addRowError($error, $rowNum);
        }
    }

    /**
     * Add errors about missing columns
     *
     * @param array $errors
     * @return void
     */
    private function addMissingColumnsError(array $errors): void
    {
        if ($errors) {
            $this->errorAggregator->addError(
                ImportAbstractEntity::ERROR_CODE_COLUMN_NOT_FOUND,
                ProcessingError::ERROR_LEVEL_CRITICAL,
                null,
                implode('", "', $errors),
                __('The following required columns are missing: %1', implode('", "', $errors))
            );
        }
    }

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string|Phrase $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     */
    private function addRowError(
        string|Phrase $errorCode,
        int $errorRowNum
    ): void {
        $this->errorAggregator->addError(
            (string)$errorCode,
            ProcessingError::ERROR_LEVEL_CRITICAL,
            $errorRowNum,
            null,
            null,
            null
        );
    }
}
