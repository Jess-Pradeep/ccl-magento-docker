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
namespace Aheadworks\Ctq\Model\Quote\Address;

use Magento\Quote\Model\Quote;

/**
 * Class ShippingInfoChecker
 *
 * @package Aheadworks\Ctq\Model\Quote\Address
 */
class ShippingInfoChecker
{
    /**
     * @var array
     */
    private $requiredAddressFields;

    /**
     * @var array
     */
    private $defaultRequiredFields = [
        'firstname',
        'lastname',
        'street',
        'city',
        'country_id'
    ];

    /**
     * @param array $requiredAddressFields
     */
    public function __construct(
        $requiredAddressFields = []
    ) {
        $this->requiredAddressFields = array_merge($requiredAddressFields, $this->defaultRequiredFields);
    }


    /**
     * Check if quote address is specified by customer
     *
     * @param Quote $quote
     * @return bool
     */
    public function isInfoSpecified($quote)
    {
        $address = $quote->getShippingAddress();
        foreach ($this->requiredAddressFields as $requiredField) {
            if (!$address->getData($requiredField)) {
                return false;
            }
        }

        return true;
    }
}
