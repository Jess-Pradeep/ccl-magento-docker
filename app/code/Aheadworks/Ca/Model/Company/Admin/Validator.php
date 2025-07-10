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

namespace Aheadworks\Ca\Model\Company\Admin;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use \Aheadworks\Ca\Model\Service\CompanyUserService;
use Magento\Framework\Exception\LocalizedException;

class Validator
{
    /**
     * @param CompanyUserService $companyUserService
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        private readonly CompanyUserService $companyUserService,
        private readonly CompanyRepositoryInterface $companyRepository
    ) {
    }

    /**
     * Validate company before share admin address list
     *
     * @param bool $shareAddressFlag
     * @param int $companyId
     * @return bool
     * @throws LocalizedException
     */
    public function validate(bool $shareAddressFlag, int $companyId): bool
    {
        $currentUser = $this->companyUserService->getCurrentUser();
        if (!$currentUser) {
            throw new LocalizedException(__('User doesn\'t belong to any company'));
        }
        if ($currentUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId() != $companyId) {
            throw new LocalizedException(__('User doesn\'t belong to current company'));
        }
        if (!$currentUser->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot()) {
            throw new LocalizedException(__('Current user is not a company admin'));
        }
        $company = $this->companyRepository->get($companyId);

        if ($company->getIsSharedAddressesFlagEnabled() == $shareAddressFlag) {
            throw new LocalizedException(__('The shared list of admin addresses has the same status'));
        }

        return true;
    }
}
