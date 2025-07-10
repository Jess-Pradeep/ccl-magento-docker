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

namespace Aheadworks\Ca\Model\Import\Company\Converter;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Aheadworks\Ca\Model\Export\Config as ExportConfig;
use Aheadworks\Ca\Model\Export\ExportEntity;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Prepare customer object
 */
class Customer
{
    /**
     * @param DataProcessor $dataProcessor
     * @param ExportConfig $exportConfig
     * @param RequestInterfaceFactory $requestFactory
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Utils $utils
     */
    public function __construct(
        private readonly DataProcessor $dataProcessor,
        private readonly ExportConfig $exportConfig,
        private readonly RequestInterfaceFactory $requestFactory,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly Utils $utils
    ) {
    }

    /**
     * Converts to object
     *
     * @param array $dataRow
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    public function convert(array $dataRow): CustomerInterface
    {
        $rootUserFieldPrefix = $this->exportConfig->getCsvFieldPrefix(
            ExportEntity::COMPANY_ENTITY_TYPE,
            ExportConfig::ROOT_USER_FIELDSET
        );
        $rootUserFieldsToImport = $this->utils->getEntityData($dataRow, ExportConfig::ROOT_USER_FIELDSET);
        $companyUserExtAttr = [
            'extension_attributes' => [
                'aw_ca_company_user' => $this->utils->removePrefix($rootUserFieldsToImport, $rootUserFieldPrefix)
            ]
        ];

        $companyAdminPrefix = $this->exportConfig->getCsvFieldPrefix(
            ExportEntity::COMPANY_ENTITY_TYPE,
            ExportConfig::COMPANY_ADMIN_FIELDSET
        );
        $companyAdminFields = $this->utils->getEntityData($dataRow, ExportConfig::COMPANY_ADMIN_FIELDSET);
        $companyAdminFields = $this->utils->removePrefix($companyAdminFields, $companyAdminPrefix);

        $rootCompanyUserData = [];
        if (isset($dataRow[CompanyInterface::ID])) {
            $rootCompanyUser = $this->companyUserManagement->getRootUserForCompany($dataRow[CompanyInterface::ID]);
            if ($rootCompanyUser && $rootCompanyUser->getId()) {
                $rootCompanyUserData = $this->dataObjectProcessor->buildOutputDataArray(
                    $rootCompanyUser,
                    CustomerInterface::class
                );
            }
        }

        $requestParams = array_replace_recursive($rootCompanyUserData, $companyAdminFields, $companyUserExtAttr);
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $request->setParams($requestParams);
        return $this->dataProcessor->prepareCustomer($request);
    }
}
