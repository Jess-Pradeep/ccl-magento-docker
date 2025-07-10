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

namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Model\FileSystem\Export\Csv as ExportCsv;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;

class DownloadSample extends Action
{
    /**
     * Sample file name
     */
    const SAMPLE_FILE_NAME = 'Sample.csv';

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ExportCsv $exportCsv
     * @param array $sampleData
     */
    public function __construct(
        Context $context,
        private readonly FileFactory $fileFactory,
        private readonly ExportCsv $exportCsv,
        private readonly array $sampleData = []
    ) {
        parent::__construct($context);
    }

    /**
     * Download sample file for import
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function execute()
    {
        $fileContent = $this->exportCsv->createFile($this->sampleData);
        return $this->fileFactory->create(self::SAMPLE_FILE_NAME, $fileContent, DirectoryList::VAR_DIR);
    }
}
