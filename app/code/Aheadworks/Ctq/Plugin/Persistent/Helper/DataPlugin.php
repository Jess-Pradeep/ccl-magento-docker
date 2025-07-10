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

namespace Aheadworks\Ctq\Plugin\Persistent\Helper;

use Magento\Framework\Event\Observer;
use Magento\Persistent\Helper\Data;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Model\QuoteList\Checker as QuoteChecker;

class DataPlugin
{
    /**
     * @param QuoteChecker $quoteChecker
     */
    public function __construct(
        private QuoteChecker $quoteChecker
    ) {}

    /**
     * Disable all actions for Aheadworks_CTQ quote
     *
     * @param Data $subject
     * @param bool $result
     * @param Observer $observer
     * @return bool
     */
    public function afterCanProcess(Data $subject, bool $result, Observer $observer): bool
    {
        /** @var $quote CartInterface */
        $quote = $observer->getEvent()->getQuote();
        if ($quote && $this->quoteChecker->isAwCtqQuote($quote)) {
            $result = false;
        }

        return $result;
    }
}
