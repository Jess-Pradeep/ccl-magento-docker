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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Plugin\NegotiableQuote\Model\Query;

use Magento\NegotiableQuote\Model\Query\GetList;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class GetListPlugin
 */
class GetListPlugin
{
    const MAIN_TABLE = 'main_table';

    /**
     * @var array
     */
    private $replacementColumns = [
        'customer_id',
        'customer_email',
        'store_id',
    ];

    /**
     * Fix ambigous error before join our table aw_ctq_quote
     *
     * @param GetList $subject
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $snapshots [optional]
     * @return array
     */
    public function beforeGetList($subject, $searchCriteria, $snapshots = false)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (in_array($filter->getField(), $this->replacementColumns)) {
                    $filter->setField(self::MAIN_TABLE . '.' . $filter->getField());
                }
            }
        }

        return [$searchCriteria, $snapshots];
    }
}