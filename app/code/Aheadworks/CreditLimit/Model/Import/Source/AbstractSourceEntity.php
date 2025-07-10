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

namespace Aheadworks\CreditLimit\Model\Import\Source;

use Aheadworks\CreditLimit\Model\Import\Processor\Composite;
use Aheadworks\CreditLimit\Model\Data\Processor\ProcessorInterface as DataProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Aheadworks\CreditLimit\Model\Config\Source\Import\Types;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractSourceEntity
 */
class AbstractSourceEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected $masterAttributeCode;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @var array
     */
    protected $requiredColumnNames;

    /**
     * AbstractSourceEntity constructor.
     *
     * @param DataProcessorInterface $dataProcessor
     * @param Composite $importProcessor
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param Helper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param string $masterAttributeCode
     * @param string $entityCode
     * @param array $requiredColumnNames
     * @param array $data
     */
    public function __construct(
        private DataProcessorInterface $dataProcessor,
        private Composite $importProcessor,
        StringUtils $string,
        protected ScopeConfigInterface $scopeConfig,
        protected ImportFactory $importFactory,
        protected Helper $resourceHelper,
        protected ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        private CustomerCollectionFactory $customerCollectionFactory,
        string $masterAttributeCode = '',
        string $entityCode = '',
        array $requiredColumnNames = [],
        array $data = []
    ) {
        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $data
        );
        $this->masterAttributeCode = $masterAttributeCode;
        $this->entityCode = $entityCode;
        $this->requiredColumnNames = $requiredColumnNames;
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    protected function _importData(): bool
    {
        $this->saveAndReplaceEntity();

        return true;
    }

    /**
     * Imported entity type code getter
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return $this->entityCode;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int|string $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        foreach ($this->requiredColumnNames as $column) {
            $columnData = $rowData[$column] ?? null;
            if (!$columnData) {
                $errorCode = str_replace(' ', '', ucwords(str_replace('_', ' ', $column)));
                $errorMessage = sprintf('%s field is required', $column);
                $this->addRowError(
                    $errorCode . 'IsRequired',
                    $rowNum,
                    $column,
                    $errorMessage
                );
            }
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Save and replace entity
     * @return void
     * @throws LocalizedException
     */
    protected function saveAndReplaceEntity(): void
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $this->addWebsiteData($bunch);
            foreach ($bunch as $rowData) {
                $rowData = $this->dataProcessor->process($rowData);
                $this->importProcessor->saveEntity($rowData, $this->getEntityTypeCode());
            }
        }
    }

    /**
     * Add website data
     *
     * @param array $rows
     * @return void
     * @throws LocalizedException
     */
    private function addWebsiteData(array &$rows): void
    {
        if ($this->getEntityTypeCode() === Types::CREDIT_UPDATE_CUSTOMERS) {
            $emails = [];
            foreach ($rows as $rowData) {
                if (isset($rowData['customer_email'])) {
                    $emails[] = $rowData['customer_email'];
                }
            }

            $customerCollection = $this->customerCollectionFactory->create()
                ->addAttributeToSelect(['email', 'website_id'])
                ->addFieldToFilter('email', ['in' => $emails])
                ->groupByEmail();

            /** @var Customer $customer */
            foreach ($customerCollection as $customer) {
                foreach ($rows as $rowKey => $rowData) {
                    if (isset($rowData['customer_email']) && $customer->getEmail() === $rowData['customer_email']) {
                        $rows[$rowKey]['website_id'] = $customer->getWebsiteId();
                    }
                }
            }
        }
    }
}
