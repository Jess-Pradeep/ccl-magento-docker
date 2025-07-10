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

namespace Aheadworks\Ctq\Plugin\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use Aheadworks\Ctq\Model\Carrier\Custom;

/**
 * Class AddressPlugin
 */
class AddressPlugin
{
    /**
     * @param Custom $customCarrier
     */
    public function __construct(
        private readonly Custom $customCarrier
    ) {}

    /**
     * Set shipping rate amount
     *
     * @param Address $subject
     * @param array $result
     * @return array
     */
    public function afterGetAllShippingRates(Address $subject, array $result): array
    {
        foreach ($result as $rate) {
            if ($rate->getCode() === 'aw_ctq_custom_aw_ctq_custom' && $this->customCarrier->isChangeAmount()) {
                $rate->setPrice($this->customCarrier->getAmount());
            }
        }

        return $result;
    }

    /**
     * Set shipping rate amount
     *
     * @param Address $subject
     * @param array $result
     * @return array
     */
    public function afterGetGroupedAllShippingRates(Address $subject, array $result): array
    {
        foreach ($result as $items) {
            foreach ($items as $rate) {
                if ($rate->getCode() === 'aw_ctq_custom_aw_ctq_custom' && $this->customCarrier->isChangeAmount()) {
                    $rate->setPrice($this->customCarrier->getAmount());
                }
            }
        }

        return $result;
    }

}
