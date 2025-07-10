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

use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;
use Aheadworks\Ca\Api\CompanyAdminCandidateRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterfaceFactory;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Source\Company\Admin\Candidate\Status as CandidateStatus;
use Aheadworks\Ca\Model\Company\Admin\Candidate\Validator as CandidateValidator;
use Aheadworks\Ca\Model\Company\Admin\Candidate\Checker as CandidateChecker;
use Magento\Framework\Exception\NoSuchEntityException;

class CandidateManagement implements CompanyAdminCandidateManagementInterface
{
    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CandidateValidator $candidateValidator
     * @param CompanyAdminCandidateInterfaceFactory $candidateFactory
     * @param CompanyAdminCandidateRepositoryInterface $candidateRepository
     * @param CandidateChecker $checker
     * @param Notifier $notifier
     */
    public function __construct(
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly CandidateValidator $candidateValidator,
        private readonly CompanyAdminCandidateInterfaceFactory $candidateFactory,
        private readonly CompanyAdminCandidateRepositoryInterface $candidateRepository,
        private readonly CandidateChecker $checker,
        private readonly Notifier $notifier
    ) {
    }

    /**
     * Create candidate to be company admin
     *
     * @param int $customerId
     * @param int $companyId
     * @return CompanyAdminCandidateInterface
     * @throws LocalizedException
     */
    public function create(int $customerId, int $companyId): CompanyAdminCandidateInterface
    {
        $this->candidateValidator->validateNewCandidate($customerId, $companyId);
        /** @var CompanyAdminCandidateInterface $candidate */
        $candidate = $this->candidateFactory->create();
        $candidate
            ->setCustomerId($customerId)
            ->setCompanyId($companyId)
            ->setStatus(CandidateStatus::PENDING_APPROVAL);

        $this->candidateRepository->save($candidate);
        $this->notifier->notifyOnCreate($candidate);

        return $candidate;
    }

    /**
     * Check if approve is required
     *
     * @param int $companyId
     * @return bool
     * @throws LocalizedException
     */
    public function isApproveRequired(int $companyId): bool
    {
        return $this->checker->checkIfPendingCandidateExists($companyId);
    }

    /**
     * Approve candidate
     *
     * @param int $candidateId
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function approve(int $candidateId): bool
    {
        $candidate = $this->candidateRepository->get($candidateId);
        $result = $this->companyUserManagement->assignNewAdminToCompany(
            $candidate->getCustomerId(),
            $candidate->getCompanyId()
        );
        if ($result) {
            $candidate->setStatus(CandidateStatus::APPROVED);
            $this->candidateRepository->save($candidate);
        }

        return $result;
    }

    /**
     * Decline candidate
     *
     * @param int $candidateId
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function decline(int $candidateId): bool
    {
        $candidate = $this->candidateRepository->get($candidateId);
        $candidate->setStatus(CandidateStatus::DECLINED);
        $this->candidateRepository->save($candidate);
        $this->notifier->notifyOnDecline($candidate);

        return true;
    }
}
