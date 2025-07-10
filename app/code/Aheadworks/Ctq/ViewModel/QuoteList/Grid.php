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
namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Tax\Helper\Data as TaxData;

/**
 * Class Grid
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class Grid implements ArgumentInterface
{
    /**
     * @var TaxData
     */
    private $taxHelper;

    /**
     * @param TaxData $taxHelper
     */
    public function __construct(
        TaxData $taxHelper
    ) {
        $this->taxHelper = $taxHelper;
    }

    /**
     * Get is display both prices
     *
     * @return bool
     */
    public function getIsDisplayBothPrices()
    {
        return $this->taxHelper->displayCartBothPrices();
    }
}
