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

namespace Aheadworks\Ca\Controller\User\Import;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\Import\SampleFileProvider;

class DownloadSample implements HttpGetActionInterface
{
    /**
     * Sample file name
     */
    private const SAMPLE_FILE_NAME = 'company_user.csv';

    /**
     * @param FileFactory $fileFactory
     * @param RawFactory $resultRawFactory
     * @param SampleFileProvider $sampleFileProvider
     */
    public function __construct(
        private readonly FileFactory $fileFactory,
        private readonly RawFactory $resultRawFactory,
        private readonly SampleFileProvider $sampleFileProvider
    ) {
    }

    /**
     * Download sample file for import
     *
     * @return ResultInterface
     * @throws NoSuchEntityException|FileSystemException
     */
    public function execute(): ResultInterface
    {
        $fileContents = $this->sampleFileProvider->getFileContents(self::SAMPLE_FILE_NAME);
        $fileSize = $this->sampleFileProvider->getSize(self::SAMPLE_FILE_NAME);

        $this->fileFactory->create(
            self::SAMPLE_FILE_NAME,
            null,
            DirectoryList::VAR_IMPORT_EXPORT,
            'application/octet-stream',
            $fileSize
        );

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($fileContents);

        return $resultRaw;
    }
}
