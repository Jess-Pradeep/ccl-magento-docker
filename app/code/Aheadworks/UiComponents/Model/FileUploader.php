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
 * @package    UiComponents
 * @version    1.0.5
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\UiComponents\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class FileUploader
{
    /**
     * @var string
     */
    const FILE_DIR = 'aw_uicomponents/imports';

    /**
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly UploaderFactory $uploaderFactory,
        private readonly Filesystem      $filesystem,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Save file to temp directory
     *
     * @param string $fileId
     * @return array
     */
    public function saveToTmpFolder(string $fileId): array
    {
        try {
            $result = ['file' => '', 'size' => '', 'name' => '', 'path' => ''];
            $mediaDirectory = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath(self::FILE_DIR);
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->getAllowedExtensions());
            $result = array_intersect_key($uploader->save($mediaDirectory), $result);

            $result['url'] = $this->getMediaUrl($result['file']);
            $result['full_path'] = $this->getFullPath($result);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * Get file url
     *
     * @param string $file
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(string $file): string
    {
        $file = ltrim(str_replace('\\', '/', $file), '/');
        return $this->storeManager
                ->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::FILE_DIR . '/' . $file;
    }

    /**
     * Get full file path
     *
     * @param string[] $data
     * @return string
     */
    public function getFullPath(array $data): string
    {
        $DS = DIRECTORY_SEPARATOR;

        $fullPath = '';
        if (isset($data['path']) && $data['path'] && isset($data['file']) && $data['file']) {
            $fullPath = $data['path'] . $DS . $data['file'];
        }
        return $fullPath;
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions(): array
    {
        return ['csv'];
    }
}
