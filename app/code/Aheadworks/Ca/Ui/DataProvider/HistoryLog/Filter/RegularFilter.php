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

namespace Aheadworks\Ca\Ui\DataProvider\HistoryLog\Filter;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Class RegularFilter
 */
class RegularFilter implements FilterApplierInterface
{
    /**
     * Apply filter
     *
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     * @throws LocalizedException
     */
    public function apply(Collection $collection, Filter $filter)
    {
        if ($filter->getField() === HistoryLogInterface::IP) {
            $value = str_replace('%', '', $filter->getValue());
            if (preg_match('/^(\d+\.){3}\d+$/', $value)) {
                $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => ip2long($value)]);
            }
            $collection->addFieldToFilter($filter->getField(), ['ntoa' => $filter->getValue()]);
        } else {
            $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
        }
    }
}
