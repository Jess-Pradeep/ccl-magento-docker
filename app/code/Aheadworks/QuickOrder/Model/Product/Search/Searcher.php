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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Product\Search;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\DataObject;
use Magento\Search\Api\SearchInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplierFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Searcher
{
    /**
     * Page size
     */
    private const RESULT_SIZE = 6;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchInterface $search
     * @param SearchResultApplierFactory $searchResultApplierFactory
     * @param ProductCollectionFactory $collectionFactory
     */
    public function __construct(
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SearchInterface $search,
        private SearchResultApplierFactory $searchResultApplierFactory,
        private ProductCollectionFactory $collectionFactory
    ) {
    }

    /**
     * Make search using provided search item
     *
     * @param string $searchTerm
     * @return DataObject[]|ProductInterface[]
     */
    public function search($searchTerm)
    {
        $searchResult = $this->runSearchEngine($searchTerm);
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $orders = [
            'relevance'=> 'DESC',
            'entity_id' => 'DESC'
        ];
        $applier = $this->searchResultApplierFactory->create(
            [
                'collection' => $collection,
                'searchResult' => $searchResult,
                'orders' => $orders,
            ]
        );
        $applier->apply();

        return $collection->getItems();
    }

    /**
     * Run search engine and return relevant results
     *
     * @param string $searchTerm
     * @return SearchResultInterface
     */
    private function runSearchEngine($searchTerm)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->setRequestName('quick_search_container')
            ->setPageSize(self::RESULT_SIZE)
            ->addFilter('search_term', $searchTerm)
            ->addFilter('visibility', [
                Visibility::VISIBILITY_IN_SEARCH, Visibility::VISIBILITY_BOTH
            ], 'in')
            ->addSortOrder('entity_id', 'ASC')
            ->build();

        return $this->search->search($searchCriteria);
    }
}
