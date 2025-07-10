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

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class Sell
 */
class Sell extends Save
{
    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @param QuoteInterface $quote
     * @return Redirect
     */
    protected function redirectTo(Redirect $resultRedirect, QuoteInterface $quote): Redirect
    {
        $this->sellerQuoteManagement->sell($quote);
        $this->updateProcessor->updateNativeQuoteSession($quote);
        return $resultRedirect->setPath('sales/order_create');
    }
}
