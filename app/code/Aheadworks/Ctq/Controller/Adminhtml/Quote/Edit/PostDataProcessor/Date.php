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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Formatter\DateTime;
use Magento\Framework\Exception\LocalizedException;

class Date implements ProcessorInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * Prepare dates for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        if (isset($data['quote']) && is_array($data['quote'])) {
            $data['quote'] = $this->processQuoteDates($data['quote']);
        }

        return $data;
    }

    /**
     * Process quote dates
     *
     * @param array $data
     * @return array
     */
    private function processQuoteDates($data)
    {
        if (empty($data[QuoteInterface::EXPIRATION_DATE])) {
            $data[QuoteInterface::EXPIRATION_DATE] = null;
        } else {
            if ($this->dateTime->getTodayDate() > $this->dateTime->getDate($data[QuoteInterface::EXPIRATION_DATE])) {
                throw new LocalizedException(__('Expiration date cannot be in the past'));
            }
            try {
                $data[QuoteInterface::EXPIRATION_DATE]
                    = $this->dateTime->getDate($data[QuoteInterface::EXPIRATION_DATE], true);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('Invalid input date format %1', $data[QuoteInterface::EXPIRATION_DATE])
                );
            }
        }

        if (empty($data[QuoteInterface::REMINDER_DATE])) {
            $data[QuoteInterface::REMINDER_DATE] = null;
        } else {
            if ($this->dateTime->getTodayDate() > $this->dateTime->getDate($data[QuoteInterface::REMINDER_DATE])) {
                throw new LocalizedException(__('Reminder date cannot be in the past'));
            }
            try {
                $data[QuoteInterface::REMINDER_DATE]
                    = $this->dateTime->getDate($data[QuoteInterface::REMINDER_DATE], true);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('Invalid input date format %1', $data[QuoteInterface::REMINDER_DATE])
                );
            }
        }

        if ($data[QuoteInterface::REMINDER_DATE]) {
            $data[QuoteInterface::REMINDER_DATE] = $this->dateTime->prepareDateToEndOfTheDay($data[QuoteInterface::REMINDER_DATE]);
        }
        if ($data[QuoteInterface::EXPIRATION_DATE]) {
            $data[QuoteInterface::EXPIRATION_DATE] = $this->dateTime->prepareDateToEndOfTheDay($data[QuoteInterface::EXPIRATION_DATE]);
        }

        return $data;
    }
}
