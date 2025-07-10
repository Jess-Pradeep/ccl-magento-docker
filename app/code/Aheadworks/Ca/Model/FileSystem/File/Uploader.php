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

namespace Aheadworks\Ca\Model\FileSystem\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Framework\Math\Random;
use Magento\ImportExport\Helper\Data as DataHelper;
use Magento\ImportExport\Model\Import\AbstractSource;
use Magento\ImportExport\Model\Import\Adapter;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Laminas\Validator\File\Upload as FileUploadValidator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Uploader
{
    /**
     * @var WriteInterface
     */
    private WriteInterface $varDirectory;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param DataHelper $importExportData
     * @param FileTransferFactory $httpFactory
     * @param UploaderFactory $uploaderFactory
     * @param Random $random
     * @param Filesystem $filesystem
     * @throws FileSystemException
     */
    public function __construct(
        private readonly DataHelper $importExportData,
        private readonly FileTransferFactory $httpFactory,
        private readonly UploaderFactory $uploaderFactory,
        private readonly Random $random,
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_IMPORT_EXPORT);
    }

    /**
     * Upload file
     *
     * @param string $fileName
     * @return string Source file path
     * @throws LocalizedException
     */
    public function upload(string $fileName): string
    {
        $adapter = $this->httpFactory->create();
        if (!$adapter->isValid($fileName)) {
            $errors = $adapter->getErrors();
            if ($errors[0] == FileUploadValidator::INI_SIZE) {
                $errorMessage = $this->importExportData->getMaxUploadSizeMessage();
            } else {
                $errorMessage = __('The file was not uploaded.');
            }
            throw new LocalizedException($errorMessage);
        }

        $uploader = $this->uploaderFactory->create(['fileId' => $fileName]);
        $uploader->setAllowedExtensions(['csv']);
        $randomFileName = $this->random->getRandomString(32) . '.' . $uploader->getFileExtension();
        try {
            $result = $uploader->save($this->getWorkingDir(), $randomFileName);
        } catch (\Exception $e) {
            throw new LocalizedException(__('The file cannot be uploaded.'));
        }

        $extension = '';
        $uploadedFile = '';
        if ($result !== false) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $extension = pathinfo($result['file'], PATHINFO_EXTENSION);
            $uploadedFile = $result['path'] . $result['file'];
        }

        if (!$extension) {
            $this->varDirectory->delete($uploadedFile);
            throw new LocalizedException(__('The file you uploaded has no extension.'));
        }
        $sourceFile = $this->getWorkingDir() . 'aw_ca_file';
        $sourceFileRelative = $this->varDirectory->getRelativePath($sourceFile .= '.' . $extension);

        if (strtolower($uploadedFile) != strtolower($sourceFile)) {
            if ($this->varDirectory->isExist($sourceFileRelative)) {
                $this->varDirectory->delete($sourceFileRelative);
            }

            try {
                $this->varDirectory->renameFile(
                    $this->varDirectory->getRelativePath($uploadedFile),
                    $sourceFileRelative
                );
            } catch (FileSystemException $e) {
                throw new LocalizedException(__('The source file moving process failed.'));
            }
        }
        $this->removeBom($sourceFile);
        return $sourceFile;
    }

    /**
     * Provide source instance
     *
     * @param string $sourceFilePath
     * @return AbstractSource
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function getSource(string $sourceFilePath): AbstractSource
    {
        try {
            $source = Adapter::findAdapterFor(
                $sourceFilePath,
                $this->filesystem->getDirectoryWrite(DirectoryList::ROOT),
                ','
            );
        } catch (\Exception $e) {
            $this->varDirectory->delete($this->varDirectory->getRelativePath($sourceFilePath));
            throw new LocalizedException(__($e->getMessage()));
        }

        return $source;
    }

    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    private function getWorkingDir(): string
    {
        return $this->varDirectory->getAbsolutePath('aw_ca_import/');
    }

    /**
     * Remove BOM from a file
     *
     * @param string $sourceFile
     * @return void
     * @throws FileSystemException
     */
    private function removeBom(string $sourceFile): void
    {
        $driver = $this->varDirectory->getDriver();
        $string = $driver->fileGetContents($this->varDirectory->getAbsolutePath($sourceFile));
        if ($string && substr($string, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
            $string = substr($string, 3);
            $driver->filePutContents($this->varDirectory->getAbsolutePath($sourceFile), $string);
        }
    }
}
