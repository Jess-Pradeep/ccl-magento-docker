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

namespace Aheadworks\Ca\Model\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Helper\Data as DataHelper;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Aheadworks\Ca\Model\Import\Command\Pool as CommandPool;

/**
 * Review import entity
 */
class ImportEntity extends AbstractEntity
{
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @param JsonHelper $jsonHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param ResourceHelper $resourceHelper
     * @param DataHelper $dataHelper
     * @param ImportData $importData
     * @param ValidatorInterface $validator
     * @param CommandPool $commandPool
     * @param string $entityType
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceHelper $resourceHelper,
        DataHelper $dataHelper,
        ImportData $importData,
        private readonly ValidatorInterface $validator,
        private readonly CommandPool $commandPool,
        private readonly string $entityType
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->errorAggregator = $errorAggregator;
        $this->_resourceHelper = $resourceHelper;
        $this->_importExportData = $dataHelper;
        $this->_dataSourceModel = $importData;
    }

    /**
     * Import data rows
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function _importData(): bool
    {
        $command = $this->commandPool->getCommand($this->getBehavior());
        $methodName = method_exists($this->_dataSourceModel, 'getNextUniqueBunch')
            ? 'getNextUniqueBunch'
            : 'getNextBunch';

        $methodParams = method_exists($this->_dataSourceModel, 'getNextUniqueBunch')
            ? $this->getIds()
            : null;

        while ($bunch = $this->_dataSourceModel->{$methodName}($methodParams)) {
            $command->execute($bunch, $this);
        }

        return true;
    }

    /**
     * Increment created items count
     *
     * @return void
     */
    public function incrementCreatedItemsCount(): void
    {
        $this->countItemsCreated ++;
    }

    /**
     * Increment updated items count
     *
     * @return void
     */
    public function incrementUpdatedItemsCount(): void
    {
        $this->countItemsUpdated ++;
    }

    /**
     * EAV entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return $this->entityType;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        $result = $this->validator->validate($rowData, $rowNum);
        if ($result->isValid()) {
            return true;
        }

        foreach ($result->getErrors() as $error) {
            $this->addRowError($error, $rowNum);
        }

        return false;
    }
}
