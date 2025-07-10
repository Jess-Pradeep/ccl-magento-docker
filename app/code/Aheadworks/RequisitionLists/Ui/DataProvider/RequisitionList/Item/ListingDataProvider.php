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
namespace Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList\Item;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ListingDataProvider
 * @package Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList\Item
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var RequisitionListRepositoryInterface
     */
    private $requisitionListRepository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerSession $customerSession
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerSession $customerSession,
        RequisitionListRepositoryInterface $requisitionListRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->customerSession = $customerSession;
        $this->requisitionListRepository = $requisitionListRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        $listId = $this->request->getParam(RequisitionListInterface::LIST_ID);
        if ($this->isListBelongToCustomer($listId)) {
            $filter = $this->filterBuilder
                ->setField(RequisitionListInterface::LIST_ID)
                ->setValue($listId)
                ->setConditionType('eq')->create();
            $this->addFilter($filter);

            return parent::getSearchCriteria();
        }

        throw new NoSuchEntityException(__('Something went wrong while render Requisition List.'));
    }

    /**
     * Retrieve config data
     *
     * @return array
     */
    public function getConfigData(): array
    {
        $configData = parent::getConfigData();
        $configData['list_id'] = $this->request->getParam(RequisitionListInterface::LIST_ID);

        return $configData;
    }

    /**
     * Check is list belong to current customer
     *
     * @param int $listId
     * @return bool
     */
    private function isListBelongToCustomer($listId)
    {
        $customerId = $this->customerSession->getCustomerId();
        try {
            $list = $this->requisitionListRepository->get($listId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $list->getCustomerId() == $customerId;
    }
}
