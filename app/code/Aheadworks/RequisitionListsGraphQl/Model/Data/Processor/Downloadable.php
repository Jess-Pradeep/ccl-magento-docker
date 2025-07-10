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

class Downloadable implements DataProcessorInterface
{
    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        if (!empty($data['downloadable_links'])) {
            $dowloadableLinks = $data['downloadable_links'];

            foreach ($dowloadableLinks as $option) {
                $data['links'][] = $option['link_id'];
            }

            unset($data['downloadable_links']);
        }

        return $data;
    }
}
