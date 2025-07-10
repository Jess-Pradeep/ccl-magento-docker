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

namespace Aheadworks\Ca\Model\Company\Admin\Candidate;

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Magento\Framework\Exception\LocalizedException;

class Validator
{
    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param Checker $checker
     */
    public function __construct(
        private readonly CompanyUserProvider $companyUserProvider,
        private readonly Checker $checker
    ) {
    }

    /**
     * Validate new company admin candidate
     *
     * @param int $customerId
     * @param int $companyId
     * @return bool
     * @throws LocalizedException
     */
    public function validateNewCandidate(int $customerId, int $companyId): bool
    {
        $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($customerId);
        if (!$companyUser) {
            throw new LocalizedException(__('User doesn\'t belong to any company'));
        }
        if ($companyUser->getCompanyId() != $companyId) {
            throw new LocalizedException(__('User doesn\'t belong to current company'));
        }

        if ($this->checker->checkIfPendingCandidateExists($companyId)) {
            throw new LocalizedException(__('There is a new Company Administrator waiting for approval'));
        }

        return true;
    }
}
