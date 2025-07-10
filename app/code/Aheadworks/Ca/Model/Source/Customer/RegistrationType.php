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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class RegistrationType
 *
 * @package Aheadworks\Ca\Model\Source\Customer
 */
class RegistrationType implements OptionSourceInterface
{
    /**#@+
     * Registration type list
     */
    const CUSTOMER = 'customer';
    const COMPANY = 'company';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CUSTOMER,
                'label' => __('Customers')
            ],
            [
                'value' => self::COMPANY,
                'label' => __('Companies')
            ]
        ];
    }
}
