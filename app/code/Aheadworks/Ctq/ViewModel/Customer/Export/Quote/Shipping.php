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
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Aheadworks\Ctq\Model\Quote\Address\ShippingInfoChecker;

/**
 * Class Shipping
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Export\Quote
 */
class Shipping implements ArgumentInterface
{
    /**
     * @var ShippingInfoChecker
     */
    private $shippingInfoChecker;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var AddressConfig
     */
    private $addressConfig;

    /**
     * @param ShippingInfoChecker $shippingInfoChecker
     * @param AddressRenderer $addressRenderer
     * @param AddressConfig $addressConfig
     */
    public function __construct(
        ShippingInfoChecker $shippingInfoChecker,
        AddressRenderer $addressRenderer,
        AddressConfig $addressConfig
    ) {
        $this->shippingInfoChecker = $shippingInfoChecker;
        $this->addressRenderer = $addressRenderer;
        $this->addressConfig = $addressConfig;
    }

    /**
     * Check if address is specified by customer
     *
     * @param Quote $quote
     * @return bool
     */
    public function isShippingInfoSpecified($quote)
    {
        return $this->shippingInfoChecker->isInfoSpecified($quote);
    }

    /**
     * Returns string with formatted address
     *
     * @param Quote $quote
     * @return string
     */
    public function getFormattedAddress($quote)
    {
        $this->addressConfig->setStore($quote->getStoreId());
        $formatType = $this->addressConfig->getFormatByCode('html');
        if (!$formatType || !$formatType->getRenderer()) {
            return '';
        }

        return $formatType->getRenderer()->renderArray($quote->getShippingAddress()->getData());
    }

    /**
     * Get formatted shipping method
     *
     * @param Quote $quote
     * @return string
     */
    public function getFormattedShippingMethod($quote)
    {
        $method = '';
        $shippingAddress = $quote->getShippingAddress();
        $selectedRate = $this->getSelectedRate(
            $shippingAddress->getGroupedAllShippingRates(),
            $shippingAddress->getShippingMethod()
        );
        if ($selectedRate) {
            $shippingDescription = $selectedRate->getCarrierTitle() . ' - ' . $selectedRate->getMethodTitle();
            $method = trim($shippingDescription, ' -');
        }

        return $method;
    }

    /**
     * Get selected shipping rate
     *
     * @param array $shippingRates
     * @param string $shippingMethod
     * @return Rate|null
     */
    private function getSelectedRate($shippingRates, $shippingMethod)
    {
        if (is_array($shippingRates)) {
            foreach ($shippingRates as $group) {
                foreach ($group as $rate) {
                    if ($rate->getCode() == $shippingMethod) {
                        return $rate;
                    }
                }
            }
        }

        return null;
    }
}
