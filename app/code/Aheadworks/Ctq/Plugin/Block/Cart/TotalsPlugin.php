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
namespace Aheadworks\Ctq\Plugin\Block\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Block\Cart\Totals;
use Aheadworks\Ctq\Block\Email\Quote\Details as QuoteDetails;
use Aheadworks\Ctq\ViewModel\Email\Quote\Details as ViewModelDetails;

/**
 * Class TotalsPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Block\Cart
 */
class TotalsPlugin
{
    /**
     * Add custom quote before processing total
     *
     * @param Totals $subject
     * @throws LocalizedException
     */
    public function beforeGetQuote(Totals $subject)
    {
        /** @var QuoteDetails $ctqBlock */
        $ctqBlock = $subject->getLayout()->getBlock('aw_ctq.email.quote.details');
        if ($ctqBlock && !$subject->getData('custom_quote')) {
            /** @var ViewModelDetails $viewModel */
            $viewModel = $ctqBlock->getViewModel();
            $subject->setData('custom_quote', $viewModel->getCart($ctqBlock->getQuote()));
        }
    }
}
