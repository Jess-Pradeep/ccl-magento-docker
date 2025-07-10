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
 * @package    RequisitionListsGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionListsGraphQl\Model\Data;

class CompositeProcessor implements DataProcessorInterface
{
    /**
     * @param DataProcessorInterface[] $processors
     */
    public function __construct(
        private readonly array $processors = []
    ) {
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof DataProcessorInterface) {
                $data = $processor->prepareData($data);
            }
        }

        return $data;
    }
}
