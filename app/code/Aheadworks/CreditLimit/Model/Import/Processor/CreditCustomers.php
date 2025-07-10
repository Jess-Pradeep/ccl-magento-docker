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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Import\Processor;

use Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerInterfaceFactory;
use Aheadworks\CreditLimit\Model\Import\MessageManager;
use Aheadworks\CreditLimit\Api\Data\Import\CreditCustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Model\ThirdPartyModule\Aheadworks\Ca\Model\Service\CompanyManagementService;

/**
 * Class CreditCustomers
 */
class CreditCustomers extends AbstractImport
{
    /**
     * CreditCustomers constructor.
     *
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param CreditCustomerInterfaceFactory $creditCustomerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param CompanyManagementService $companyManagementService
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $configEntity
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        private CreditCustomerInterfaceFactory $creditCustomerDataFactory,
        private DataObjectHelper $dataObjectHelper,
        private CreditLimitManagementInterface $creditLimitManagement,
        private CompanyManagementService $companyManagementService,
        private CustomerRepositoryInterface $customerRepository,
        array $configEntity = []
    ) {
        parent::__construct($import, $importConfig, $messageManager, $configEntity);
    }

    /**
     * Save entity
     *
     * @param array $rowData
     * @param null|string $type
     * @return bool
     */
    public function saveEntity(array $rowData, ?string $type = null): bool
    {
        $creditCustomerData = $this->creditCustomerDataFactory->create();
        try {
            $rowData[CreditCustomerInterface::AMOUNT_TO_ADD] = (float)$rowData[CreditCustomerInterface::AMOUNT_TO_ADD];
            $this->dataObjectHelper->populateWithArray(
                $creditCustomerData,
                $rowData,
                CreditCustomerInterface::class
            );
            $customer = $this->customerRepository->get(
                $creditCustomerData->getCustomerEmail(),
                $rowData['website_id'] ?? null
            );
            if (!$this->companyManagementService->isCustomerInCompany($customer->getId())) {
                $this->creditLimitManagement->updateCreditBalance(
                    $customer->getId(),
                    $creditCustomerData->getAmountToAdd(),
                    $creditCustomerData->getCurrency(),
                    $creditCustomerData->getCommentToAdmin(),
                    $creditCustomerData->getCommentToCustomer(),
                    $creditCustomerData->getPoNumber()
                );
            }
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}
