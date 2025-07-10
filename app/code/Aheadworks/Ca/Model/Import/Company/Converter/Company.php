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
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Aheadworks\Ca\Model\CompanyRepository;
use Aheadworks\Ca\Model\Export\Config as ExportConfig;
use Aheadworks\Ca\Model\Export\ExportEntity;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Prepare company object
 */
class Company
{
    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataProcessor $dataProcessor
     * @param ExportConfig $exportConfig
     * @param CompanyRepository $companyRepository
     * @param RequestInterfaceFactory $requestFactory
     * @param Utils $utils
     */
    public function __construct(
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly DataProcessor $dataProcessor,
        private readonly ExportConfig $exportConfig,
        private readonly CompanyRepository $companyRepository,
        private readonly RequestInterfaceFactory $requestFactory,
        private readonly Utils $utils
    ) {
    }

    /**
     * Converts to company object
     *
     * @param array $dataRow
     * @return CompanyInterface
     * @throws NoSuchEntityException
     */
    public function convert(array $dataRow): CompanyInterface
    {
        $companyFieldPrefix = $this->exportConfig->getCsvFieldPrefix(
            ExportEntity::COMPANY_ENTITY_TYPE,
            ExportConfig::COMPANY_FIELDSET
        );
        $companyFieldsToImport = $this->utils->getEntityData($dataRow, ExportConfig::COMPANY_FIELDSET);
        $companyFieldsOriginal = [];
        if (isset($companyFieldsToImport[CompanyInterface::ID])) {
            try {
                $originalCompany = $this->companyRepository->get($companyFieldsToImport[CompanyInterface::ID]);
                $companyFieldsOriginal = $this->dataObjectProcessor->buildOutputDataArray(
                    $originalCompany,
                    CompanyInterface::class
                );
            } catch (NoSuchEntityException) {
                $companyFieldsOriginal = [];
            }
        }

        $companyFieldsToImport = $this->utils->removePrefix($companyFieldsToImport, $companyFieldPrefix);
        $requestParams = [
            'company' => array_replace($companyFieldsOriginal, $companyFieldsToImport)
        ];

        $adminFieldPrefix = $this->exportConfig->getCsvFieldPrefix(
            ExportEntity::COMPANY_ENTITY_TYPE,
            ExportConfig::COMPANY_ADMIN_FIELDSET
        );
        if (isset($dataRow[$adminFieldPrefix . '_' . CustomerInterface::GROUP_ID])) {
            $requestParams[CustomerInterface::GROUP_ID]
                = $dataRow[$adminFieldPrefix . '_' . CustomerInterface::GROUP_ID];
        }

        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $request->setParams($requestParams);
        return $this->dataProcessor->prepareCompany($request);
    }
}
