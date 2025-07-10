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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Filesystem;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class FileResponseFactory
 *
 * @package Aheadworks\Ctq\Model\Filesystem
 */
class FileResponseFactory
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @param FileFactory $fileFactory
     */
    public function __construct(
        FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
    }

    /**
     * Create file as HTTP response
     *
     * @param string $fileContent
     * @param string $fileName
     * @param bool $needToRemove
     * @return ResponseInterface
     * @throws \Exception
     */
    public function create($fileContent, $fileName, $needToRemove = true)
    {
        $content['type'] = 'string';
        $content['value'] = $fileContent;
        $content['rm'] = $needToRemove;

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
