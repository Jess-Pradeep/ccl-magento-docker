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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Formatter;

use Magento\Framework\Stdlib\DateTime as FrameworkDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as DateConversion;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateTime
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var DateConversion
     */
    private $dateTime;

    /**
     * @param TimezoneInterface $timezone
     * @param DateConversion $dateTime
     */
    public function __construct(
        TimezoneInterface $timezone,
        DateConversion $dateTime
    ) {
        $this->timezone = $timezone;
        $this->dateTime = $dateTime;
    }

    /**
     * Convert date to given format
     *
     * @param string|null $date
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function formatDate(?string $date, string $format = FrameworkDateTime::DATETIME_PHP_FORMAT)
    {
        if ($date) {
            $dateTime = new \DateTime($date);
            $date = $dateTime->format($format);
        }

        return $date;
    }

    /**
     * Retrieve date
     *
     * @param string $date
     * @param bool $withTime
     * @return string
     */
    public function getDate(string $date, bool $withTime = false): string
    {
        return $this->getFormattedDate($date, $withTime);
    }

    /**
     * Get timestamp
     *
     * @param null|string $date
     * @return int
     */
    public function getTimestamp(?string $date = null): int
    {
        return $this->dateTime->timestamp($date);
    }

    /**
     * Retrieve today date
     *
     * @param bool $withTime
     * @return string
     */
    public function getTodayDate(bool $withTime = false): string
    {
        return $this->getFormattedDate(null, $withTime);
    }

    /**
     * Prepare Date to end of the day
     *
     * @param string $date
     * @param $format
     * @return string
     * @throws \Exception
     */
    public function prepareDateToEndOfTheDay(string $date, $format = FrameworkDateTime::DATETIME_PHP_FORMAT): string
    {
        try {
            $newDate = new \DateTime(
                $date,
                new \DateTimeZone($this->timezone->getConfigTimezone())
            );
            $newDate->setTimezone(new \DateTimeZone('UTC'));
            $newDate->setTime(23, 59, 59);

            return $newDate->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Retrieve formatted date
     *
     * @param string|null $date
     * @param bool $withTime
     * @return string
     */
    private function getFormattedDate(?string $date = null, bool $withTime = false): string
    {
        $format = $withTime ? FrameworkDateTime::DATETIME_PHP_FORMAT : FrameworkDateTime::DATE_PHP_FORMAT;
        return $this->date($date)->format($format);
    }

    /**
     * Retrieve \DateTime object for current locale
     *
     * @param null|string $date
     * @param string|null $locale
     * @param bool $useTimezone
     * @return \DateTime
     */
    private function date(?string $date = null, ?string $locale = null, bool $useTimezone = false): \DateTime
    {
        return $this->timezone->date($date ? strtotime($date) : false, $locale, $useTimezone);
    }
}
