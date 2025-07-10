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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Domain;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Domain
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $userManagement;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CompanyUserManagementInterface $userManagement
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
        CompanyUserManagementInterface $userManagement,
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
        $this->userManagement = $userManagement;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        /** @var CustomerInterface|null $user */
        $user = $this->userManagement->getCurrentUser();
        if ($user) {
            $this->addCompanyFilter($user);
        }
        return parent::getSearchCriteria();
    }

    /**
     * Add company filter
     *
     * @param CustomerInterface $user
     */
    private function addCompanyFilter($user)
    {
        $filter = $this->filterBuilder
            ->setField(CompanyUserInterface::COMPANY_ID)
            ->setValue($user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId())
            ->setConditionType('eq')->create();
        $this->addFilter($filter);
    }
}
