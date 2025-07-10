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

namespace Aheadworks\Ca\Model\Import\User\Validator;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Import\ValidatorInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Row validation class
 */
class IntegrityValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    public const REQUIRED_FIELDS = [
        CustomerInterface::FIRSTNAME,
        CustomerInterface::LASTNAME,
        CustomerInterface::EMAIL,
        CompanyUserInterface::COMPANY_ROLE_ID
    ];

    /**
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        private readonly ValidationResultFactory $validationResultFactory
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
        $errors = [];
        foreach (self::REQUIRED_FIELDS as $requiredField) {
            if (!isset($rowData[$requiredField])) {
                $errors[] = __('Missing required column "%1"', $requiredField);
            }
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
