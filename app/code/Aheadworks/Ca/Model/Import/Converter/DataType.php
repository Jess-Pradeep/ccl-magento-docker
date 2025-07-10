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

namespace Aheadworks\Ca\Model\Import\Converter;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Converts imported data row according to its data type
 */
class DataType
{
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Prepare data row
     *
     * @param array $columns
     * @param array $dataRow
     * @return array
     */
    public function prepareDataRow(array $columns, array $dataRow): array
    {
        foreach ($columns as $column) {
            if ($column['dataType'] == 'array') {
                $dataRow[$column['code']] = isset($dataRow[$column['code']]) && !empty($dataRow[$column['code']])
                    ? explode(',', $dataRow[$column['code']])
                    : [];
            }
            if ($column['dataType'] == 'serializable') {
                $dataRow[$column['code']] = isset($dataRow[$column['code']]) && !empty($dataRow[$column['code']])
                    ? $this->serializer->unserialize($dataRow[$column['code']])
                    : [];
            }
        }

        return $dataRow;
    }
}
