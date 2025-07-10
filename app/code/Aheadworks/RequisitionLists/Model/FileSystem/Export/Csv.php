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

namespace Aheadworks\RequisitionLists\Model\FileSystem\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

class Csv
{
    /**
     * File name
     */
    const FILENAME = 'sample.csv';

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * Create csv file with content
     *
     * @param array $content
     * @param string $baseDir
     * @return array
     * @throws FileSystemException
     */
    public function createFile(
        array $content,
        string $baseDir = DirectoryList::VAR_DIR
    ): array {
        $directory = $this->filesystem->getDirectoryWrite($baseDir);
        $directory->create('export');
        $file = 'export/' . self::FILENAME;
        $stream = $directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($content['header']);
        foreach ($content['rows'] ?? [] as $line) {
            $stream->writeCsv($line);
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }
}
