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

namespace Aheadworks\RequisitionListsGraphQl\Model\Data\Processor;

use Aheadworks\RequisitionListsGraphQl\Model\Data\DataProcessorInterface;

class Configurable implements DataProcessorInterface
{
    /**
     * Prepare configurable data
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        if (!empty($data['configurable_options'])) {
            $configurableOptions = $data['configurable_options'];

            foreach ($configurableOptions as $option) {
                $data['super_attribute'][$option['attribute_id']] = $option['option_id'];
            }

            unset($data['configurable_options']);
        }

        return $data;
    }
}
