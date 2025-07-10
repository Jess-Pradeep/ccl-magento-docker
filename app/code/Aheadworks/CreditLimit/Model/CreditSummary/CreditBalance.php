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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\CreditSummary;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\CreditSummary\Checker as CreditSummaryChecker;
use Aheadworks\CreditLimit\Model\DateTime\Manager as DateTimeManager;

class CreditBalance
{
    /**
     * @param CreditSummaryChecker $creditSummaryChecker
     * @param DateTimeManager $dateTimeManager
     */
    public function __construct(
        private readonly CreditSummaryChecker $creditSummaryChecker,
        private readonly DateTimeManager $dateTimeManager
    ) {
    }

    /**
     * Set negative credit balance date
     *
     * @param SummaryInterface $summary
     * @return void
     * @throws \Exception
     */
    public function setNegativeBalanceDate(SummaryInterface $summary): void
    {
        $negativeBalanceDate = $summary->getNegativeBalanceDate();
        $isCreditBalancePositive = $this->creditSummaryChecker->isCreditBalancePositive($summary);

        if ($isCreditBalancePositive && $negativeBalanceDate) {
            $summary->setNegativeBalanceDate(null);
        }
        if (!$isCreditBalancePositive && !$negativeBalanceDate) {
            $summary->setNegativeBalanceDate($this->dateTimeManager->getFormattedCurrentDate());
        }
    }
}
