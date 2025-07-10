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
namespace Aheadworks\RequisitionLists\CustomerData;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Http\Context;

/**
 * Class RequisitionList
 * @package Aheadworks\RequisitionLists\CustomerData
 */
class RequisitionList implements SectionSourceInterface
{
    /**
     * Requisition Lists key in sections
     */
    const REQUISITION_LISTS_KEY = 'lists';

    /**
     * @var RequisitionListRepositoryInterface
     */
    private $requisitionListRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Context $context
     */
    public function __construct(
        RequisitionListRepositoryInterface $requisitionListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Context $context
    ) {
        $this->requisitionListRepository = $requisitionListRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $lists = $this->getLists();
        $data = [];
        foreach ($lists as $list) {
            $data[] = [
                RequisitionListInterface::LIST_ID => $list->getListId(),
                RequisitionListInterface::NAME => $list->getName()
            ];
        }

        return [self::REQUISITION_LISTS_KEY => $data];
    }

    /**
     * Get current customer id from context
     *
     * @return int
     */
    private function getCurrentCustomerId()
    {
        return (int)$this->context->getValue(RequisitionListInterface::CUSTOMER_ID);
    }

    /**
     * Get customer Requisition Lists
     *
     * @return RequisitionListInterface[]
     */
    private function getLists()
    {
        return $this->requisitionListRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(RequisitionListInterface::CUSTOMER_ID, $this->getCurrentCustomerId())
                    ->create()
            )->getItems();
    }
}
