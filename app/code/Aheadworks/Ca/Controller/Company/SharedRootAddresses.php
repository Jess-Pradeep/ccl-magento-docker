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

namespace Aheadworks\Ca\Controller\Company;

use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class SharedRootAddresses implements HttpGetActionInterface
{
    /**
     * @param JsonFactory $jsonResultFactory
     * @param Session $customerSession
     * @param Provider $companyUserProvider
     * @param RequestInterface $request
     * @param CompanySharedAddressManagementInterface $companySharedAddressService
     */
    public function __construct(
        private readonly JsonFactory $jsonResultFactory,
        private readonly Session $customerSession,
        private readonly Provider $companyUserProvider,
        private readonly RequestInterface $request,
        private readonly CompanySharedAddressManagementInterface $companySharedAddressService
    ) {
    }

    /**
     * Execute action based on request and return result
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $isChecked = $this->request->getParam('isChecked');
        $customerId = $this->customerSession->getCustomerId();
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);

        $response = ['status' => 'success'];
        if (isset($isChecked) && $companyUser && $companyUser->getIsRoot()) {
            try {
                $this->companySharedAddressService->setIsAddressListSharedToAllUsers(
                    $isChecked === 'true',
                    (int)$companyUser->getCompanyId()
                );
            } catch (NoSuchEntityException|LocalizedException) {
                $response = ['status' => 'error', 'message' => __('Sorry, something went wrong. Please try again later.')];
            }
        } else {
            $response = ['status' => 'error', 'message' => __('Sorry, something went wrong. Please try again later.')];
        }
        $result->setData($response);

        return $result;
    }
}
