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

namespace Aheadworks\RequisitionLists\Model\Import;

class FileContentProcessor
{
    /**
     * @param FileContentProcessorInterface[] $processors
     */
    public function __construct(
        private readonly array $processors = []
    ) {
    }

    /**
     * Retrieve file data
     *
     * @param string $filePath
     * @return array
     */
    public function process(string $filePath): array
    {
        $data = [];
        $type = $this->getFileExtension($filePath);

        if (!empty($this->processors[$type]) && $this->processors[$type] instanceof FileContentProcessorInterface) {
            $data = $this->processors[$type]->process($filePath);
        }

        return $data;
    }

    /**
     * Retrieve file extension
     *
     * @param string $file
     * @return string
     */
    private function getFileExtension(string $file): string
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }
}
