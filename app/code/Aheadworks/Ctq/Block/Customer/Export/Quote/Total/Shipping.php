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
namespace Aheadworks\Ctq\Block\Customer\Export\Quote\Total;

use Magento\Tax\Block\Checkout\Shipping as TaxShipping;

/**
 * Class Shipping
 * @package Aheadworks\Ctq\Block\Customer\Export\Quote\Total
 */
class Shipping extends TaxShipping
{
    /**
     * @inheritDoc
     */
    public function displayShipping()
    {
        return $this->getTotal()->getValue();
    }
}
