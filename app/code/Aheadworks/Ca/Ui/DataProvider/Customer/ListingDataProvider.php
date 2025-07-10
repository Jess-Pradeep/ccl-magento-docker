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

namespace Aheadworks\Ca\Ui\DataProvider\Customer;

use Magento\Customer\Model\Config\Share as ShareConfig;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Customer\Ui\Component\DataProvider;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

class ListingDataProvider extends DataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AttributeRepository $attributeRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ShareConfig $shareConfig
     * @param AppState $appState
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AttributeRepository $attributeRepository,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly ShareConfig $shareConfig,
        private readonly AppState $appState,
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
            $attributeRepository,
            $meta,
            $data
        );
    }

    /**
     * Returns Search result
     *
     * @return SearchResultInterface
     * @throws LocalizedException
     */
    public function getSearchResult(): SearchResultInterface
    {
        $searchResult = parent::getSearchResult();
        $companyId = $this->request->getParam('company_id');
        if ($companyId
            && $this->appState->getAreaCode() == Area::AREA_ADMINHTML
            && !$this->shareConfig->isGlobalScope()
        ) {
            $rootUser = $this->companyUserManagement->getRootUserForCompany($companyId);
            $searchResult->getSelect()->where('website_id = ?', $rootUser->getWebsiteId());
        }

        return $searchResult;
    }
}
