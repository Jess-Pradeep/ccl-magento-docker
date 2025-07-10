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

namespace Aheadworks\Ctq\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Owner
 */
class Owner implements OptionSourceInterface
{
    /**#@+
     * Constants defined for RMA status types
     */
    const SELLER = 'seller';
    const BUYER = 'buyer';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SELLER, 'label' => __('Seller')],
            ['value' => self::BUYER, 'label' => __('Buyer')]
        ];
    }

    /**
     * Retrieve options
     *
     * @param string $code
     * @return array|null
     */
    public function getOptionByCode(string $code): ?array
    {
        foreach ($this->toOptionArray() as $optionItem) {
            if ($optionItem['value'] === $code) {
                return $optionItem;
            }
        }

        return null;
    }
}
