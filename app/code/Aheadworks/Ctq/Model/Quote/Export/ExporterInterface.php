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
namespace Aheadworks\Ctq\Model\Quote\Export;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ExporterInterface
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
interface ExporterInterface
{
    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @param string $methodToLocate
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function exportQuote($quote, $methodToLocate = Locator::LOCATE_BY_ID);
}
