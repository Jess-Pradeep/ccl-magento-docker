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

namespace Aheadworks\RequisitionListsGraphQl\Model\DataProveder;

use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

class RequisitionList implements DataProviderInterface
{
    /**
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     */
    public function __construct(
        private readonly RequisitionListRepositoryInterface $requisitionListRepository
    ) {
    }

    /**
     * Retrieve data
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getListData(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        return $this->requisitionListRepository->getList($searchCriteria);
    }
}
