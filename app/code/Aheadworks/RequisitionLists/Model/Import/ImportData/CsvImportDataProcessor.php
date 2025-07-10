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

namespace Aheadworks\RequisitionLists\Model\Import\ImportData;

use Aheadworks\RequisitionLists\Model\Import\PrepareImportDataInterface;

class CsvImportDataProcessor implements PrepareImportDataInterface
{
    /**
     * @param FieldResolverInterface $fieldResolver
     */
    public function __construct(
        private readonly FieldResolverInterface $fieldResolver
    ) {
    }

    /**
     * Prepare import data
     *
     * @param array $data
     * @return array
     */
    public function prepare(array $data): array
    {
        $lineItems = [];
        $header = $data[0] ?? [];

        try {
            if ($header) {
                unset($data[0]);
                foreach ($data as $keyData => $lineData) {
                    foreach ($header as $keyHeader => $field) {
                        if (!empty($lineData[$keyHeader])) {
                            $resolvedData = $this->fieldResolver->resolveData($lineData[$keyHeader], mb_strtolower($field));
                            $lineItems[$keyData][$field] = $resolvedData ?: $lineData[$keyHeader];
                        }
                    }
                }
            }
        } catch (\Error) {
        }

        return $lineItems;
    }
}
