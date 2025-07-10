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
namespace Aheadworks\Ca\Controller\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Aheadworks\Ca\Controller\Company\DataProcessor\DataProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;

/**
 * Class DataProcessor
 */
class DataProcessor
{
    /**
     * @var CompanyInterfaceFactory
     */
    private $companyFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var DataProcessorInterface[]
     */
    private $companyProcessors = [];

    /**
     * @var DataProcessorInterface[]
     */
    private $customerProcessors = [];

    /**
     * @param CompanyInterfaceFactory $companyFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CustomerExtractor $customerExtractor
     * @param DataProcessorInterface[] $companyProcessors
     * @param DataProcessorInterface[] $customerProcessors
     */
    public function __construct(
        CompanyInterfaceFactory $companyFactory,
        DataObjectHelper $dataObjectHelper,
        CustomerExtractor $customerExtractor,
        array $companyProcessors = [],
        array $customerProcessors = []
    ) {
        $this->companyFactory = $companyFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerExtractor = $customerExtractor;
        $this->companyProcessors = $companyProcessors;
        $this->customerProcessors = $customerProcessors;
    }

    /**
     * Prepare company
     *
     * @param RequestInterface $request
     * @return CompanyInterface
     */
    public function prepareCompany($request)
    {
        /** @var CompanyInterface $company */
        $company = $this->companyFactory->create();

        $data = $request->getParam('company', []);
        foreach ($this->companyProcessors as $processor) {
            $data = $processor->process($data);
        }

        $this->dataObjectHelper->populateWithArray(
            $company,
            $data,
            CompanyInterface::class
        );

        $company->setCustomerGroupId($request->getParam('group_id'));
        return $company;
    }

    /**
     * Prepare customer
     *
     * @param RequestInterface $request
     * @return CustomerInterface
     */
    public function prepareCustomer($request)
    {
        $customer = $this->customerExtractor->extract(
            'customer_account_create',
            $request
        );

        $data = $request->getParams();
        if (!empty($data['dob'])) {
            $data['dob'] = $customer->getDob();
        }

        foreach ($this->customerProcessors as $processor) {
            $data = $processor->process($data);
        }

        $this->dataObjectHelper->populateWithArray(
            $customer,
            $data,
            CustomerInterface::class
        );

        return $customer;
    }
}
