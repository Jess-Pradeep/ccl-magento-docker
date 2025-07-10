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

namespace Aheadworks\CreditLimit\Model\PaymentReminder;

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Model\CreditSummary\Search\Builder as SearchBuilder;
use Aheadworks\CreditLimit\Model\DateTime\Manager as DateTimeManager;

class ProcessNotifier
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param SearchBuilder $searchBuilder
     * @param DateTimeManager $dateTimeManager
     * @param Notifier $notifier
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $config,
        private readonly SearchBuilder $searchBuilder,
        private readonly DateTimeManager $dateTimeManager,
        private readonly Notifier $notifier
    ) {
    }

    /**
     * Processing payment reminders for today
     *
     * @return void
     * @throws \Exception
     */
    public function processPaymentReminderForToday(): void
    {
        $reminderXDayByWebsites = $this->getReminderXDayByWebsites();

        $creditSummaries = $this->searchBuilder
            ->addWebsitesFilter(array_keys($reminderXDayByWebsites))
            ->addNegativeBalanceDateFilter()
            ->searchSummaries();

        foreach ($creditSummaries as $creditSummary) {
            $currentDayDiff = $this->dateTimeManager->getDateDiffInDays($creditSummary->getNegativeBalanceDate());
            $expectedDayDiff = $reminderXDayByWebsites[$creditSummary->getWebsiteId()];

            if ($currentDayDiff && $this->checkIfMult($expectedDayDiff, $currentDayDiff)) {
                $this->notifier->notify($creditSummary);
            }
        }
    }

    /**
     * Get array days by website
     *
     * @return array
     */
    private function getReminderXDayByWebsites(): array
    {
        $list = [];
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = (int)$website->getId();
            if ($xDay = $this->config->getSendPaymentReminderDays($websiteId)) {
                $list[$websiteId] = $xDay;
            }
        }
        return $list;
    }

    /**
     * Check if a number is a multiple of the input
     *
     * @param int $input
     * @param int $toBeChecked
     * @return bool
     */
    private function checkIfMult(int $input, int $toBeChecked): bool
    {
        return $toBeChecked % $input === 0;
    }
}
