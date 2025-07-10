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

class Checker
{
    /**
     * Is credit balance positive
     *
     * @param SummaryInterface $summary
     * @return bool
     */
    public function isCreditBalancePositive(SummaryInterface $summary): bool
    {
        return (float)$summary->getCreditBalance() >= 0;
    }
}
