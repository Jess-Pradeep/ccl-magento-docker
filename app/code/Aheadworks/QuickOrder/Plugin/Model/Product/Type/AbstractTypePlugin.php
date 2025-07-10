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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Plugin\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\AbstractType;

class AbstractTypePlugin
{
    /**
     * Storage for already added files
     *
     * @var array
     */
    private $storage = [];

    /**
     * Disable adding to queue upload, if file already added
     *
     * @param AbstractType $subject
     * @param array $queueOptions
     * @return array
     */
    public function beforeAddFileQueue(AbstractType $subject, array $queueOptions): array
    {
        $storageItem = $this->prepareStorageItem($queueOptions);
        if ($this->isCanBeAdded($storageItem)) {
            $this->storage[] = $storageItem;
        } else {
            $queueOptions = [];
        }

        return [$queueOptions];
    }

    /**
     * Prepare item to store
     *
     * @param array $queueItem
     * @return array
     */
    private function prepareStorageItem(array $queueItem): array
    {
        $queueItemUploader = $queueItem['uploader'] ?? null;
        $srcName = $queueItem['src_name'];
        $tmpPath = '';
        if ($queueItemUploader) {
            $tmpPath = $queueItemUploader->getFileInfo()[$srcName]
                ? $queueItemUploader->getFileInfo()[$srcName]['tmp_name']
                : null;
        }

        return [
            'src_name' => $srcName,
            'operation' => $queueItem['operation'],
            'tmp_name' => $tmpPath
        ];
    }

    /**
     * Is item can be added
     *
     * @param array $itemToAdd
     * @return bool
     */
    private function isCanBeAdded(array $itemToAdd): bool
    {
        return !in_array($itemToAdd, $this->storage);
    }
}
