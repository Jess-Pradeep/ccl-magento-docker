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

namespace Aheadworks\Ca\Model\Export\Filter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Ca\Model\Export\FilterProcessorInterface;

/**
 * Multiselect frontend input filter
 */
class MultiselectFilter implements FilterProcessorInterface
{
    /**
     * Process
     *
     * @param AbstractCollection $collection
     * @param string $filterName
     * @param array|string $filterValue
     * @return void
     */
    public function process(AbstractCollection $collection, string $filterName, array|string $filterValue): void
    {
        if (is_array($filterValue) && !empty($filterValue)) {
            $collection->addFieldToFilter($filterName, ['in' => $filterValue]);
        }
    }
}
