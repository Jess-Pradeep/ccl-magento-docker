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

class PrepareImportData
{
    /**
     * @param array $prepareImportDataProcessors
     */
    public function __construct(
        private readonly array $prepareImportDataProcessors = []
    ) {
    }

    /**
     * Prepare file data
     *
     * @param array $data
     * @param string $type
     * @return array
       */
    public function prepare(array $data, string $type): array
    {
        $result = [];

        if (!empty($this->prepareImportDataProcessors[$type])
            && $this->prepareImportDataProcessors[$type] instanceof PrepareImportDataInterface
        ) {
            $result = $this->prepareImportDataProcessors[$type]->prepare($data);
        }

        return $result;
    }
}
