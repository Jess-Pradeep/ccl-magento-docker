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
namespace Aheadworks\Ctq\Model\Quote\Expiration;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class Calculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration
 */
class Calculator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Calculate expiration date
     *
     * @param int|null $storeId
     * @return null|string
     * @throws \Exception
     */
    public function calculateExpirationDate($storeId = null)
    {
        $expirationDate = null;
        $daysOffset = $this->config->getQuoteExpirationPeriodInDays($storeId);
        if ($daysOffset) {
            $today = new \DateTime('today', new \DateTimeZone('UTC'));
            $today->add(new \DateInterval('P' . $daysOffset . 'D'));
            $today->setTime(23, 59, 59);
            $expirationDate = $today->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }

        return $expirationDate;
    }

    /**
     * Calculate reminder date
     *
     * @param int|null $storeId
     * @return null|string
     * @throws \Exception
     */
    public function calculateReminderDate(?int $storeId): ?string
    {
        $reminderDate = null;
        $expirationDate = $this->calculateExpirationDate($storeId);
        $daysOffset = $this->config->getSendEmailReminderInDays($storeId);
        if ($expirationDate && $daysOffset) {
            $date = new \DateTime($expirationDate, new \DateTimeZone('UTC'));
            $date->modify('-' . $daysOffset . 'days');
            $date->setTime(23, 59, 59);
            $reminderDate = $date->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }

        return $reminderDate;
    }
}
