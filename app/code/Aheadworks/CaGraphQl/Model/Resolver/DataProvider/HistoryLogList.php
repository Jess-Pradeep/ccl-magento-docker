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
 * @package    CaGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CaGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Ca\Api\HistoryLogRepositoryInterface;

class HistoryLogList implements DataProviderInterface
{
    /**
     * @param HistoryLogRepositoryInterface $historyLogRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        private readonly HistoryLogRepositoryInterface $historyLogRepository,
        private readonly DataObjectProcessor $dataObjectProcessor,
    ) {
    }

    /**
     * Retrieve data
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws LocalizedException
     */
    public function getListData(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $result = $this->historyLogRepository->getList($searchCriteria);
        $itemsAsArray = [];
        foreach ($result->getItems() as $item) {
            $itemsAsArray[] = $this->dataObjectProcessor->buildOutputDataArray(
                $item,
                HistoryLogInterface::class
            );
        }

        $result->setItems($itemsAsArray);
        return $result;
    }
}
