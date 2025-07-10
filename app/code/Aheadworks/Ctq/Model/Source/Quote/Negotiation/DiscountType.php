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

namespace Aheadworks\Ctq\Model\Source\Quote\Negotiation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DiscountType
 */
class DiscountType implements OptionSourceInterface
{
    /**#@+
     * Discount types
     */
    const PERCENTAGE_DISCOUNT = 'percent';
    const AMOUNT_DISCOUNT = 'amount';
    const PROPOSED_PRICE = 'proposed_price';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PERCENTAGE_DISCOUNT, 'label' => __('Percentage Discount')],
            ['value' => self::AMOUNT_DISCOUNT, 'label' => __('Amount Discount')],
            ['value' => self::PROPOSED_PRICE, 'label' => __('Proposed Subtotal')]
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $optionItem) {
            $options[$optionItem['value']] = ['label' => $optionItem['label']];
        }
        return $options;
    }

    /**
     * Retrieve options
     *
     * @param string|null $code
     * @return array|null
     */
    public function getOptionByCode(?string $code): ?array
    {
        foreach ($this->toOptionArray() as $optionItem) {
            if ($optionItem['value'] === $code) {
                return $optionItem;
            }
        }

        return null;
    }
}
