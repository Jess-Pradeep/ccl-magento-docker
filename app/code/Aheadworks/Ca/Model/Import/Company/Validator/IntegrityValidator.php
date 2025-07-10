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

namespace Aheadworks\Ca\Model\Import\Company\Validator;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Export\ExportEntity;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Import\ValidatorInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Aheadworks\Ca\Model\Export\Config as ExportConfig;

/**
 * Row validation class
 */
class IntegrityValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private array $requiredFields = [];

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ExportConfig $exportConfig
     */
    public function __construct(
        private readonly ValidationResultFactory $validationResultFactory,
        private readonly ExportConfig $exportConfig
    ) {
    }

    /**
     * Validate
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return ValidationResult
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(array $rowData, int $rowNumber): ValidationResult
    {
        $fieldsetList = $this->exportConfig->getFieldsetList(ExportEntity::COMPANY_ENTITY_TYPE);
        $this->requiredFields = [];
        if (!(isset($rowData[CompanyInterface::ID]) && !empty($rowData[CompanyInterface::ID]))) {
            $companyFields = [
                CompanyInterface::NAME,
                CompanyInterface::EMAIL,
                CompanyInterface::STREET,
                CompanyInterface::CITY,
                CompanyInterface::COUNTRY_ID,
                CompanyInterface::POSTCODE
            ];
            $this->addToRequired($companyFields, $fieldsetList['company']['csv_field_prefix'] ?? '');

            $customerFields = [
                CustomerInterface::EMAIL,
                CustomerInterface::FIRSTNAME,
                CustomerInterface::LASTNAME,
                CustomerInterface::WEBSITE_ID,
                CustomerInterface::GROUP_ID,
                CustomerInterface::STORE_ID
            ];
            $this->addToRequired($customerFields, $fieldsetList['company_admin']['csv_field_prefix'] ?? '');
        }

        $errors = [];
        foreach ($this->requiredFields as $requiredField) {
            if (!isset($rowData[$requiredField])) {
                $errors[] = __('Missing required column "%1"', $requiredField);
            }
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }

    /**
     * Add to required
     *
     * @param array $fields
     * @param string $prefix
     * @return void
     */
    private function addToRequired(array $fields, string $prefix): void
    {
        foreach ($fields as $field) {
            $this->requiredFields[] = $this->exportConfig->resolveCode($prefix, $field);
        }
    }
}
