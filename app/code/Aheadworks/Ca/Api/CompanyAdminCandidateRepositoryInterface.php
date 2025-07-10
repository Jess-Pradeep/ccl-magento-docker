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
 * Interface CompanyAdminCandidateRepositoryInterface
 * @api
 */
interface CompanyAdminCandidateRepositoryInterface
{
    /**
     * Save candidate
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface $candidate
     * @return \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(
        \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface $candidate
    ): \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;

    /**
     * Retrieve candidate by ID
     *
     * @param int $candidateId
     * @return \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $candidateId): \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;

    /**
     * Retrieve company domain list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\CompanyAdminCandidateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete candidate
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface $candidate
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface $candidate): bool;

    /**
     * Delete candidate by ID
     *
     * @param int $candidateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $candidateId): bool;
}
