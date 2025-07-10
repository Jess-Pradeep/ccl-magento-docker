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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Model\Import\FileContentProcessor;

use Aheadworks\RequisitionLists\Model\Import\FileContentProcessorInterface;
use Aheadworks\RequisitionLists\Model\Import\PrepareImportData;
use Magento\Framework\File\Csv;

class CsvReader implements FileContentProcessorInterface
{
    /**
     * @param Csv $csvProcessor
     * @param PrepareImportData $prepareImportData
     */
    public function __construct(
        private readonly Csv $csvProcessor,
        private readonly PrepareImportData $prepareImportData
    ) {
    }

    /**
     * Retrieve data from csv file
     *
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    public function process(string $filePath): array
    {
        $data = $this->csvProcessor->getData($filePath);

        return $this->prepareImportData->prepare($data, 'csv');
    }
}
