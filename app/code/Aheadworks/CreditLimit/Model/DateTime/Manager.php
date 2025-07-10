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

namespace Aheadworks\CreditLimit\Model\DateTime;

use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime;

class Manager
{
    /**
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        private readonly DateTimeFactory $dateTimeFactory
    ) {
    }

    /**
     * Get current date
     *
     * @param string $timezone
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function getFormattedCurrentDate(
        string $timezone = 'UTC',
        string $format = DateTime::DATETIME_PHP_FORMAT
    ): string {
        $dateTime = $this->dateTimeFactory->create('now', new \DateTimeZone($timezone));

        return $dateTime->format($format);
    }

    /**
     * Get the date difference in days from the current one
     *
     * @param string $comparableDate
     * @return int
     */
    public function getDateDiffInDays(string $comparableDate): int
    {
        $comparableDate = $this->dateTimeFactory->create($comparableDate, new \DateTimeZone('UTC'));
        $currentDate = $this->dateTimeFactory->create('now', new \DateTimeZone('UTC'));

        return (int)$comparableDate->diff($currentDate)->format("%r%a");
    }

}
