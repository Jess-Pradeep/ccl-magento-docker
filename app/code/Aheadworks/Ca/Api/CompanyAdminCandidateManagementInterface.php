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

namespace Aheadworks\Ca\Api;

/**
 * Interface CompanyAdminCandidateManagementInterface
 * @api
 */
interface CompanyAdminCandidateManagementInterface
{
    /**
     * Create candidate to be company admin
     *
     * @param int $customerId
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(int $customerId, int $companyId): \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;

    /**
     * Check if approve is required for company
     *
     * @param int $companyId
     * @return bool
     */
    public function isApproveRequired(int $companyId): bool;

    /**
     * Approve candidate
     *
     * @param int $candidateId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function approve(int $candidateId): bool;

    /**
     * Decline candidate
     *
     * @param int $candidateId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function decline(int $candidateId): bool;
}
