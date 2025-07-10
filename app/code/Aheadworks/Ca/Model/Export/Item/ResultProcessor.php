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

namespace Aheadworks\Ca\Model\Export\Item;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Export item result processor
 */
class ResultProcessor
{
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Process result
     *
     * @param array $columns
     * @param DataObject $item
     * @return array
     */
    public function processResult(array $columns, DataObject $item): array
    {
        $result = [];
        foreach ($columns as $column) {
            $result[$column['code']] = match ($column['dataType']) {
                'text' => $item->getData($column['code']) ?? '',
                'array' => $item->hasData($column['code']) && is_array($item->getData($column['code']))
                    ? implode(',', $item->getData($column['code']))
                    : '',
                'serializable' => $item->hasData($column['code']) && is_array($item->getData($column['code']))
                    ? $this->serializer->serialize($item->getData($column['code']))
                    : '',
                default => ''
            };
        }

        return $result;
    }
}
