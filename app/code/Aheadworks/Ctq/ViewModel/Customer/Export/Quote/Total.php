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
namespace Aheadworks\Ctq\ViewModel\Customer\Export\Quote;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider as TotalProvider;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Renderer as TotalRenderer;

/**
 * Class Total
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Export\Quote
 */
class Total implements ArgumentInterface
{
    /**
     * @var TotalProvider
     */
    private $totalProvider;

    /**
     * @var TotalRenderer
     */
    private $totalRenderer;

    /**
     * @param TotalProvider $totalProvider
     * @param TotalRenderer $totalRenderer
     */
    public function __construct(
        TotalProvider $totalProvider,
        TotalRenderer $totalRenderer
    ) {
        $this->totalProvider = $totalProvider;
        $this->totalRenderer = $totalRenderer;
    }

    /**
     * Render quote totals
     *
     * @param Quote $quote
     * @param string|null $area
     * @param int $colspan
     * @return string
     */
    public function renderTotals($quote, $area = null, $colspan = 1)
    {
        $totals = $this->totalProvider->getQuoteTotals($quote);
        return $this->totalRenderer->render($totals, $area, $colspan);
    }
}
