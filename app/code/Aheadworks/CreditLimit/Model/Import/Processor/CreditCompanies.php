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

use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyInterface;
use Aheadworks\CreditLimit\Model\Import\MessageManager;
use Aheadworks\CreditLimit\Model\ThirdPartyModule\Aheadworks\Ca\Model\Service\CompanyManagementService;
use Magento\Framework\Api\DataObjectHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;
use Aheadworks\CreditLimit\Api\Data\Import\CreditCompanyInterfaceFactory;

/**
 * Class CreditCompanies
 */
class CreditCompanies extends AbstractImport
{
    /**
     * CreditCompanies constructor.
     *
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param CreditCompanyInterfaceFactory $creditCompanyDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param CompanyManagementService $companyManagementService
     * @param array $configEntity
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        private CreditCompanyInterfaceFactory $creditCompanyDataFactory,
        private DataObjectHelper $dataObjectHelper,
        private CreditLimitManagementInterface $creditLimitManagement,
        private CompanyManagementService $companyManagementService,
        array $configEntity = []
    ) {
        parent::__construct($import, $importConfig, $messageManager, $configEntity);
    }

    /**
     * Save entity
     *
     * @param array $rowData
     * @param null|string $type
     * @return void
     */
    public function saveEntity(array $rowData, ?string $type = null): bool
    {
        $creditCompanyData = $this->creditCompanyDataFactory->create();
        try {
            $this->dataObjectHelper->populateWithArray(
                $creditCompanyData,
                $rowData,
                CreditCompanyInterface::class
            );
            $company = $this->companyManagementService->getCompanyByEmail($creditCompanyData->getCompanyEmail());
            if ($company) {
                $rootCompanyUser = $this->companyManagementService->getRootUserByCompanyId($company->getId());
                if ($rootCompanyUser) {
                    $this->creditLimitManagement->updateCreditBalance(
                        $rootCompanyUser->getId(),
                        $creditCompanyData->getAmountToAdd(),
                        $creditCompanyData->getCurrency(),
                        $creditCompanyData->getCommentToAdmin(),
                        $creditCompanyData->getCommentToCustomer(),
                        $creditCompanyData->getPoNumber()
                    );
                }
            }
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}
