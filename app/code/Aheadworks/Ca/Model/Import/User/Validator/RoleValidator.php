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
use Aheadworks\Ca\Model\Import\ValidatorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\ResourceModel\Role as RoleResourceModel;

/**
 * Row validation class
 */
class RoleValidator implements ValidatorInterface
{
    /**
     * @var array|null
     */
    private ?array $roleIds = [];

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param CompanyUserProvider $provider
     * @param RoleResourceModel $roleResource
     */
    public function __construct(
        private readonly ValidationResultFactory $validationResultFactory,
        private readonly CompanyUserProvider $provider,
        private readonly RoleResourceModel $roleResource
    ) {
    }

    /**
     * Validate
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return ValidationResult
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function validate(array $rowData, int $rowNumber): ValidationResult
    {
        $errors = [];
        if ($this->roleIds == null) {
            $companyUser = $this->provider->getCurrentCompanyUser();
            if (!$companyUser) {
                $errors[] = __('Current company user is undefined');
            }

            $this->roleIds = $this->roleResource->getRoleIdsByCompanyId($companyUser->getCompanyId());
        }

        if (!isset($rowData[CompanyUserInterface::COMPANY_ROLE_ID])) {
            $errors[] = __('Company role ID column is empty');
        }

        if (!in_array($rowData[CompanyUserInterface::COMPANY_ROLE_ID], $this->roleIds)) {
            $errors[] = __(
                'Provided company_role_id: "%1" doesn\'t belong to this company',
                $rowData[CompanyUserInterface::COMPANY_ROLE_ID]
            );
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
