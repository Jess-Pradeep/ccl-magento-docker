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

namespace Aheadworks\Ctq\Plugin\Quote\Model\ResourceModel;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResourceModel;
use Aheadworks\Ctq\Model\Quote\Loader as QuoteLoader;
use Aheadworks\Ctq\Model\ThirdPartyModule\ModuleChecker;

class QuotePlugin
{
    /**
     * @param QuoteLoader $quoteLoader
     * @param ModuleChecker $moduleChecker
     */
    public function __construct(
        private QuoteLoader $quoteLoader,
        private ModuleChecker $moduleChecker
    ) {}

    /**
     * Load quote data by customer identifier
     *
     * @param QuoteResourceModel $subject
     * @param callable $proceed
     * @param Quote $quote
     * @param int $customerId
     * @return Quote
     */
    public function aroundLoadByCustomerId(QuoteResourceModel $subject, callable $proceed, Quote $quote, int $customerId)
    {
        if ($this->moduleChecker->isMagentoPersistentEnabled()) {
            $result = $this->quoteLoader->loadByCustomerIdExcludeCtq($quote, $customerId);
            if (!$result->getEntityId()) {
                $result = $proceed($quote, $customerId);
            }
        } else {
            $result = $proceed($quote, $customerId);
        }

        return $result;
    }
}
