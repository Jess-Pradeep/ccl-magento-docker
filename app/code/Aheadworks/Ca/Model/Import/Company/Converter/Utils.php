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

namespace Aheadworks\Ca\Model\Import\Company\Converter;

use Aheadworks\Ca\Model\Export\Config as ExportConfig;
use Aheadworks\Ca\Model\Export\ExportEntity;

/**
 * Converter utils
 */
class Utils
{
    /**
     * @param ExportConfig $exportConfig
     */
    public function __construct(
        private readonly ExportConfig $exportConfig
    ) {
    }

    /**
     * Get entity data
     *
     * @param array $dataRow
     * @param string $fieldsetName
     * @return array
     */
    public function getEntityData(array $dataRow, string $fieldsetName): array
    {
        $fields = $this->exportConfig->getFieldsetFields(ExportEntity::COMPANY_ENTITY_TYPE, $fieldsetName);
        return array_filter(
            $dataRow,
            fn ($key) => in_array($key, $fields),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Remove prefix
     *
     * @param array $dataRow
     * @param string $prefix
     * @return array
     */
    public function removePrefix(array $dataRow, string $prefix): array
    {
        $result = [];
        foreach ($dataRow as $key => $dataItem) {
            if (str_starts_with($key, $prefix . '_')) {
                $key = substr($key, strlen($prefix . '_'));
            }

            $result[$key] = $dataItem;
        }

        return $result;
    }
}
