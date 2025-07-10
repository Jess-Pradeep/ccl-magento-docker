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

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Company\Admin\Candidate\Search\Builder as CandidateSearcher;
use Aheadworks\Ca\Model\Source\Company\Admin\Candidate\Status as CandidateStatus;

class Checker
{
    /**
     * @param CandidateSearcher $candidateSearcher
     */
    public function __construct(
        private readonly CandidateSearcher $candidateSearcher
    ) {
    }

    /**
     * Check if any candidate with pending status exists
     *
     * @param int $companyId
     * @return bool
     * @throws LocalizedException
     */
    public function checkIfPendingCandidateExists(int $companyId): bool
    {
        $this->candidateSearcher
            ->addCompanyFilter($companyId)
            ->addStatusFilter(CandidateStatus::PENDING_APPROVAL);

        $candidates = $this->candidateSearcher->searchCandidates();

        return count($candidates) > 0;
    }
}
