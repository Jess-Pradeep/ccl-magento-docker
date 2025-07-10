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

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Backend\Model\Url as BackendUrl;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Url as FrontendUrl;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Url
 */
class Url
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FrontendUrl
     */
    private $urlBuilderFrontend;

    /**
     * @var BackendUrl
     */
    private $urlBuilderBackend;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $urlBuilder
     * @param FrontendUrl $urlBuilderFrontend
     * @param BackendUrl $urlBuilderBackend
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        UrlInterface $urlBuilder,
        FrontendUrl $urlBuilderFrontend,
        BackendUrl $urlBuilderBackend,
        CustomerRegistry $customerRegistry,
        StoreManagerInterface $storeManager
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->urlBuilderFrontend = $urlBuilderFrontend;
        $this->urlBuilderBackend = $urlBuilderBackend;
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve create company url
     * @return string
     */
    public function getFrontendCreateCompanyFormUrl()
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/company/create');
    }

    /**
     * Retrieve edit customer url
     * @param $customerId
     * @return string
     */
    public function getFrontendEditCustomerUrl($customerId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/user/edit', ['id' => $customerId]);
    }

    /**
     * Retrieve customer change status url
     *
     * @param int $customerId
     * @param bool $needActivate
     * @return string
     */
    public function getFrontendCustomerChangeStatusUrl($customerId, $needActivate)
    {
        return $this->urlBuilderFrontend->getUrl(
            'aw_ca/user/changeStatus',
            [
                'id' => $customerId,
                'activate' => (int)$needActivate
            ]
        );
    }

    /**
     * Retrieve edit role url
     * @param $roleId
     * @return string
     */
    public function getFrontendEditRoleUrl($roleId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/role/edit', ['id' => $roleId]);
    }

    /**
     * Retrieve frontend company users url
     *
     * @return string
     */
    public function getFrontendCompanyUsersUrl()
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/user');
    }

    /**
     * Retrieve delete role url
     * @param $roleId
     * @return string
     */
    public function getFrontendDeleteRoleUrl($roleId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/role/delete', ['id' => $roleId]);
    }

    /**
     * Retrieve reset password url
     *
     * @param CustomerInterface|Customer $customer
     * @return string
     */
    public function getResetPasswordUrl(CustomerInterface $customer): string
    {
        $customerSecureData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $store = $this->storeManager->getStore($customer->getStoreId());

        return $this->urlBuilderFrontend->setScope($store)->getUrl(
            'customer/account/createPassword',
            ['_query' => ['id' => $customer->getId(), 'token' => $customerSecureData->getRpToken()]]
        );
    }

    /**
     * Get url to company URL in admin
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function getCompanyUrl($company)
    {
        return $this->urlBuilderBackend->getUrl('aw_ca/company/edit', ['id' => $company->getId()]);
    }

    /**
     * Retrieve order approve url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderApproveUrl($orderId)
    {
        return $this->urlBuilder->getUrl('aw_ca/order/approve', ['order_id' => $orderId]);
    }

    /**
     * Retrieve order reject url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderRejectUrl($orderId)
    {
        return $this->urlBuilder->getUrl('aw_ca/order/reject', ['order_id' => $orderId]);
    }

    /**
     * Retrieve order view frontend url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderViewFrontendUrl($orderId)
    {
        return $this->urlBuilderFrontend->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Get link to download sample file for user import
     *
     * @return string
     */
    public function getUrlToDownloadSampleFileForUserImport(): string
    {
        return $this->urlBuilder->getUrl('aw_ca/user_import/downloadSample');
    }

    /**
     * Get link to start company users importing
     *
     * @return string
     */
    public function getUrlToStartUserImport(): string
    {
        return $this->urlBuilder->getUrl('aw_ca/user_import/start');
    }
}
