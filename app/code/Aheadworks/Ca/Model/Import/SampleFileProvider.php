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

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Import sample file provider model
 *
 * This class support only *.csv.
 */
class SampleFileProvider
{
    /**
     * @param ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        private readonly ReadFactory $readFactory,
        private readonly ComponentRegistrar $componentRegistrar
    ) {
    }

    /**
     * Returns the size for the given file
     *
     * @param string $entityName
     * @throws NoSuchEntityException
     * @return int|null
     */
    public function getSize(string $entityName): ?int
    {
        $directoryRead = $this->getDirectoryRead();
        $filePath = $this->getPath($entityName);
        return isset($directoryRead->stat($filePath)['size'])
            ? $directoryRead->stat($filePath)['size'] : null;
    }

    /**
     * Returns Content for the given file associated to an Import entity
     *
     * @param string $filename
     * @return string
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    public function getFileContents(string $filename): string
    {
        $directoryRead = $this->getDirectoryRead();
        $filePath = $this->getPath($filename);

        return $directoryRead->readFile($filePath);
    }

    /**
     * Get path to file
     *
     * @param string $filename
     * @return string
     * @throws NoSuchEntityException
     */
    private function getPath(string $filename): string
    {
        $directoryRead = $this->getDirectoryRead();
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Aheadworks_Ca');
        $fileAbsolutePath = $moduleDir . '/Files/Custom/' . $filename;

        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        if (!$directoryRead->isFile($filePath)) {
            throw new NoSuchEntityException(__("There is no file: %file", ['file' => $filePath]));
        }

        return $filePath;
    }

    /**
     * Get directory read
     *
     * @return ReadInterface
     */
    private function getDirectoryRead(): ReadInterface
    {
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Aheadworks_Ca');
        return $this->readFactory->create($moduleDir);
    }
}
